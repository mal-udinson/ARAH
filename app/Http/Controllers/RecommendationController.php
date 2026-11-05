<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Rekomendasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RecommendationController extends Controller
{
    /**
     * Menampilkan rekomendasi untuk mahasiswa.
     */
    public function index(Request $request)
    {
        $mahasiswa = Mahasiswa::with([
            'asesmen',
            'nilai.mataKuliah',
            'rekomendasi.mataKuliah',
        ])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        /*
         * Hanya tampilkan rekomendasi dari semester mahasiswa saat ini.
         * Jadi rekomendasi semester lain tidak ikut tercampur.
         */
        $items = Rekomendasi::with('mataKuliah')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereHas('mataKuliah', function ($query) use ($mahasiswa) {
                $query->where('semester', $mahasiswa->semester);
            })
            ->orderByDesc('skor')
            ->get();

        return view('recommendations.index', [
            'm' => $mahasiswa,
            'items' => $items,
        ]);
    }


    /**
     * Generate rekomendasi AI.
     */
    public function generate(Request $request)
    {
        $m = Mahasiswa::with([
            'asesmen',
            'nilai.mataKuliah',
        ])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        /*
         * =========================================================
         * 1. TENTUKAN SEMESTER TARGET
         * =========================================================
         */

        $targetSemester = (int) $m->semester;


        /*
         * =========================================================
         * 2. AMBIL MATA KULIAH YANG SUDAH PERNAH DIAMBIL
         * =========================================================
         */

        $completedCourseIds = $m->nilai
            ->pluck('mata_kuliah_id')
            ->unique()
            ->values()
            ->all();


        /*
         * =========================================================
         * 3. FILTER KANDIDAT
         * =========================================================
         *
         * HANYA:
         * - semester yang sama dengan mahasiswa
         * - belum pernah punya nilai
         */

        $candidates = MataKuliah::query()
            ->where('semester', $targetSemester)
            ->when(
                !empty($completedCourseIds),
                function ($query) use ($completedCourseIds) {
                    $query->whereNotIn('id', $completedCourseIds);
                }
            )
            ->orderBy('kode')
            ->get();


        /*
         * =========================================================
         * 3B. FILTER PRASYARAT (FIX B1 + B2)
         * =========================================================
         *
         * Buang kandidat yang prasyaratnya belum ada di riwayat nilai.
         * Berlaku untuk jalur Gemini maupun fallback, karena keduanya
         * mengambil data dari $candidates.
         */

        $completedNames = MataKuliah::query()
            ->whereIn('id', $completedCourseIds)
            ->pluck('nama')
            ->map(fn ($nama) => strtolower(trim($nama)))
            ->all();

        $candidates = $candidates
            ->filter(function ($course) use ($completedNames) {

                $prasyarat = strtolower(trim((string) $course->prasyarat));

                if ($prasyarat === '' || $prasyarat === 'tidak ada') {
                    return true;
                }

                return in_array($prasyarat, $completedNames, true);
            })
            ->values();


        /*
         * Kalau tidak ada kandidat.
         */

        if ($candidates->isEmpty()) {
            return redirect()
                ->route('recommendations')
                ->with(
                    'error',
                    'Belum ada mata kuliah yang tersedia untuk direkomendasikan pada semester ini.'
                );
        }


        /*
         * =========================================================
         * 4. HITUNG RATA-RATA MINAT
         * =========================================================
         */

        $interestScores = $m->asesmen
            ->pluck('skor', 'kategori')
            ->map(fn ($score) => (float) $score)
            ->toArray();

        $overallInterest = $m->asesmen->avg('skor');

        if ($overallInterest === null) {
            $overallInterest = 0;
        }

        $overallInterest = round($overallInterest, 2);


        /*
         * =========================================================
         * 5. HITUNG KEMAMPUAN AKADEMIK PER KATEGORI
         * =========================================================
         */

        $abilityByCategory = [];

        foreach ($m->nilai as $nilai) {

            $kategori = $nilai->mataKuliah?->kategori;

            if (!$kategori) {
                continue;
            }

            $abilityByCategory[$kategori][] =
                (float) $nilai->nilai_angka;
        }


        /*
         * Rata-rata akademik keseluruhan.
         */

        $overallAbility = $m->nilai->avg('nilai_angka');

        if ($overallAbility === null) {
            $overallAbility = 0;
        }

        $overallAbility = round($overallAbility, 2);


        /*
         * Ubah menjadi rata-rata per kategori.
         */

        foreach ($abilityByCategory as $kategori => $scores) {
            $abilityByCategory[$kategori] =
                round(
                    array_sum($scores) / count($scores),
                    2
                );
        }


        /*
         * =========================================================
         * 6. HITUNG SKOR DETERMINISTIK
         * =========================================================
         *
         * 40% MINAT
         * 60% KEMAMPUAN
         */

        $candidateData = $candidates->map(function ($course) use (
            $interestScores,
            $overallInterest,
            $abilityByCategory,
            $overallAbility
        ) {

            /*
             * Kalau kategori MK ada di asesmen,
             * gunakan skor kategori tersebut.
             *
             * Kalau tidak ada (misalnya Umum/Bahasa),
             * gunakan rata-rata minat keseluruhan.
             */

            $interestScore = $interestScores[$course->kategori]
                ?? $overallInterest;


            /*
             * Kalau ada riwayat nilai untuk kategori tersebut,
             * gunakan kemampuan kategori.
             *
             * Kalau belum ada,
             * gunakan kemampuan akademik keseluruhan.
             */

            $abilityScore = $abilityByCategory[$course->kategori]
                ?? $overallAbility;


            /*
             * Formula ARAH:
             *
             * 40% Minat
             * 60% Kemampuan
             */

            $finalScore = (
                ($interestScore * 0.40) +
                ($abilityScore * 0.60)
            );

            $finalScore = round(
                min(100, max(0, $finalScore)),
                2
            );

            return [
                'id' => $course->id,
                'kode' => $course->kode,
                'nama' => $course->nama,
                'kategori' => $course->kategori,
                'sks' => $course->sks,
                'semester' => $course->semester,
                'prasyarat' => $course->prasyarat,

                'interest_score' => round(
                    $interestScore,
                    2
                ),

                'ability_score' => round(
                    $abilityScore,
                    2
                ),

                'compatibility_score' => $finalScore,
            ];
        })
            ->sortByDesc('compatibility_score')
            ->values();


        /*
         * =========================================================
         * 7. SIAPKAN DATA UNTUK GEMINI
         * =========================================================
         *
         * Gemini hanya menerima kandidat yang SUDAH DIFILTER
         * oleh Laravel.
         */

        $candidateJson = $candidateData
            ->toJson(
                JSON_PRETTY_PRINT |
                JSON_UNESCAPED_UNICODE
            );


        /*
         * =========================================================
         * 8. PROMPT NARA
         * =========================================================
         */

        $prompt = <<<PROMPT
Kamu adalah NARA, Academic Navigation Assistant pada platform ARAH.

Tugas kamu adalah memilih maksimal 5 mata kuliah paling relevan
untuk mahasiswa berdasarkan data kandidat yang diberikan.

KONDISI MAHASISWA:
- Semester: {$targetSemester}
- Rata-rata minat: {$overallInterest}
- Rata-rata kemampuan akademik: {$overallAbility}

ATURAN PENTING:
1. HANYA boleh memilih mata kuliah dari daftar kandidat.
2. Jangan membuat mata kuliah baru.
3. Jangan memilih mata kuliah di luar daftar.
4. Jangan memilih mata kuliah yang sudah diambil mahasiswa.
5. Skor compatibility_score dari sistem Laravel adalah skor resmi.
6. Jangan mengubah nilai compatibility_score.
7. Pilih maksimal 5 mata kuliah.
8. Berikan alasan singkat, jelas, dan personal untuk setiap pilihan.
9. Alasan harus menghubungkan minat dan kemampuan mahasiswa dengan mata kuliah.
10. Jangan mengatakan bahwa kamu menggantikan keputusan Dosen PA.

FORMAT OUTPUT WAJIB JSON SAJA:

[
    {
        "mata_kuliah_id": 123,
        "alasan": "Alasan singkat mengapa mata kuliah ini relevan."
    }
]

DAFTAR KANDIDAT:
{$candidateJson}
PROMPT;


        /*
         * =========================================================
         * 9. PANGGIL GEMINI
         * =========================================================
         */

        $apiKey = config('services.gemini.key');
        $model = config(
            'services.gemini.model',
            'gemini-3.5-flash-lite'
        );

        $selected = [];


        if ($apiKey) {

            try {

                $response = Http::timeout(60)
                    ->post(
                        "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                        [
                            'contents' => [
                                [
                                    'parts' => [
                                        [
                                            'text' => $prompt,
                                        ],
                                    ],
                                ],
                            ],
                            'generationConfig' => [
                                'temperature' => 0.2,
                            ],
                        ]
                    );

                if ($response->successful()) {

                    $text = $response->json(
                        'candidates.0.content.parts.0.text'
                    );

                    if ($text) {

                        /*
                         * Bersihkan kemungkinan markdown:
                         * ```json ... ```
                         */

                        $text = trim($text);

                        $text = preg_replace(
                            '/^```(?:json)?\s*/i',
                            '',
                            $text
                        );

                        $text = preg_replace(
                            '/\s*```$/',
                            '',
                            $text
                        );

                        $decoded = json_decode(
                            $text,
                            true
                        );

                        if (is_array($decoded)) {
                            $selected = $decoded;
                        }
                    }
                }

            } catch (\Throwable $e) {

                /*
                 * Kalau Gemini gagal,
                 * sistem tetap lanjut dengan ranking Laravel.
                 */
                $selected = [];
            }
        }


        /*
         * =========================================================
         * 10. VALIDASI HASIL GEMINI
         * =========================================================
         */

        $candidateMap = $candidateData
            ->keyBy('id');


        $validSelections = collect($selected)
            ->filter(function ($item) use ($candidateMap) {

                if (!is_array($item)) {
                    return false;
                }

                if (!isset($item['mata_kuliah_id'])) {
                    return false;
                }

                return $candidateMap->has(
                    (int) $item['mata_kuliah_id']
                );
            })
            ->unique('mata_kuliah_id')
            ->take(5)
            ->values();


        /*
         * =========================================================
         * 11. FALLBACK
         * =========================================================
         *
         * Kalau Gemini gagal / output invalid,
         * ambil 5 kandidat dengan skor tertinggi.
         */

        if ($validSelections->isEmpty()) {

            $validSelections = $candidateData
                ->take(5)
                ->map(function ($course) {

                    return [
                        'mata_kuliah_id' => $course['id'],

                        'alasan' =>
                            'Mata kuliah ini memiliki tingkat kecocokan '
                            . 'yang baik berdasarkan profil minat dan '
                            . 'kemampuan akademik kamu.',
                    ];
                });
        }


        /*
         * =========================================================
         * 11B. BATAS TOTAL SKS PER SEMESTER
         * =========================================================
         *
         * Total SKS rekomendasi tidak boleh melebihi batas.
         * Berlaku untuk jalur Gemini maupun fallback.
         * Urutan mengikuti urutan pilihan (Gemini atau skor tertinggi).
         */

        $batasSks = 24;
        $totalSks = 0;

        $validSelections = $validSelections
            ->filter(function ($selection) use ($candidateMap, $batasSks, &$totalSks) {

                $course = $candidateMap->get(
                    (int) $selection['mata_kuliah_id']
                );

                if (!$course) {
                    return false;
                }

                $sks = (int) $course['sks'];

                if ($totalSks + $sks > $batasSks) {
                    return false;
                }

                $totalSks += $sks;

                return true;
            })
            ->values();


        /*
         * =========================================================
         * 12. HAPUS REKOMENDASI LAMA DARI KANDIDAT SEMESTER INI
         * =========================================================
         *
         * Tidak menyentuh rekomendasi semester lain.
         */

        /*
         * Hapus SEMUA rekomendasi mahasiswa untuk mata kuliah semester ini,
         * termasuk baris lama yang sudah tidak valid (misalnya prasyarat
         * belum terpenuhi). Baris yang valid akan dibuat ulang di langkah 13.
         */

        $semesterCourseIds = MataKuliah::query()
            ->where('semester', $targetSemester)
            ->pluck('id')
            ->all();

        Rekomendasi::where('mahasiswa_id', $m->id)
            ->whereIn('mata_kuliah_id', $semesterCourseIds)
            ->delete();


        /*
         * =========================================================
         * 13. SIMPAN HASIL
         * =========================================================
         */

        foreach ($validSelections as $selection) {

            $courseId = (int) $selection['mata_kuliah_id'];

            $course = $candidateMap->get($courseId);

            if (!$course) {
                continue;
            }

            $reason = trim(
                $selection['alasan']
                ?? 'Dipilih berdasarkan tingkat kecocokan dengan profil akademik kamu.'
            );

            Rekomendasi::create([
                'mahasiswa_id' => $m->id,
                'mata_kuliah_id' => $courseId,
                'skor' => $course['compatibility_score'],
                'alasan' => $reason,
                'status' => 'menunggu',
            ]);
        }


        /*
         * =========================================================
         * 14. KEMBALI KE HALAMAN REKOMENDASI
         * =========================================================
         */

        return redirect()
            ->route('recommendations')
            ->with(
                'success',
                'NARA berhasil menganalisis rekomendasi untuk semester '
                . $targetSemester
                . '.'
            );
    }
}