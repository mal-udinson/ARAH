<?php

namespace App\Http\Controllers;

use App\Models\AsesmenMinat;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use RuntimeException;
use Throwable;

class AssessmentController extends Controller
{
    /**
     * Jangan mengganti nama kategori karena dipakai oleh
     * profil minat dan recommendation engine.
     */
    private function categories(): array
    {
        return [
            'Pemrograman' => 'Pemrograman & Pengembangan',
            'UI/UX'       => 'UI/UX & Kreativitas Digital',
            'Data'        => 'Data & Analitik',
            'Sistem'      => 'Sistem & Teknologi',
            'Bisnis'      => 'Bisnis & Manajemen Teknologi',
        ];
    }

    private function questionSessionKey(int $mahasiswaId): string
    {
        return 'assessment_questions_' . $mahasiswaId;
    }

    /**
     * Menampilkan halaman asesmen.
     * Pertanyaan dibuat NARA berdasarkan katalog mata kuliah database.
     */
    public function show(Request $request)
    {
        Log::info('ARAH_ASSESSMENT_SHOW_HIT', [
            'user_id' => $request->user()?->id,
            'path' => $request->path(),
        ]);

        $mahasiswa = Mahasiswa::where(
            'user_id',
            $request->user()->id
        )->firstOrFail();

        $sessionKey = $this->questionSessionKey(
            (int) $mahasiswa->id
        );

        $savedQuestions = $request->session()->get($sessionKey);

        // Gunakan pertanyaan yang sama jika masih tersimpan di session.
        if (
            is_array($savedQuestions) &&
            count($savedQuestions) === 20
        ) {
            $groups = $this->buildGroups($savedQuestions);

            $assessmentPayload = $this->makeAssessmentPayload(
                $mahasiswa,
                $savedQuestions
            );

            return view(
                'assessment.index',
                compact('groups', 'assessmentPayload')
            );
        }

        try {
            $courses = $this->getCourseCatalog($mahasiswa);

            if (empty($courses)) {
                throw new RuntimeException(
                    'Katalog mata kuliah kosong atau kolom nama mata kuliah tidak ditemukan.'
                );
            }

            $questions = $this->generateQuestions($courses);

            $request->session()->put(
                $sessionKey,
                $questions
            );

            $groups = $this->buildGroups($questions);

            $assessmentPayload = $this->makeAssessmentPayload(
                $mahasiswa,
                $questions
            );

            return view(
                'assessment.index',
                compact('groups', 'assessmentPayload')
            );
        } catch (Throwable $e) {
            Log::error('NARA gagal membuat pertanyaan asesmen.', [
                'user_id' => $request->user()->id,
                'mahasiswa_id' => $mahasiswa->id,
                'exception' => get_class($e),
                'error' => $e->getMessage(),
            ]);

            $message = app()->environment('local')
                ? 'Gagal membuat asesmen: ' . $e->getMessage()
                : 'NARA belum berhasil membuat pertanyaan asesmen. Silakan coba lagi.';

            return redirect()
                ->route('dashboard')
                ->with('error', $message);
        }
    }

    /**
     * Membuat payload pertanyaan untuk kompatibilitas Blade.
     * store() versi ini tidak bergantung pada payload tersebut.
     */
    private function makeAssessmentPayload(
        Mahasiswa $mahasiswa,
        array $questions
    ): string {
        return encrypt([
            'mahasiswa_id' => (int) $mahasiswa->id,
            'questions' => $questions,
            'issued_at' => now()->timestamp,
        ]);
    }

    /**
     * Mengambil katalog mata kuliah dari database.
     */
    private function getCourseCatalog(Mahasiswa $mahasiswa): array
    {
        $model = new MataKuliah();
        $table = $model->getTable();

        $query = MataKuliah::query();

        if (
            $mahasiswa->kampus_id &&
            Schema::hasColumn($table, 'kampus_id')
        ) {
            $kampusId = $mahasiswa->kampus_id;

            $query->where(function ($q) use ($kampusId) {
                $q->where('kampus_id', $kampusId)
                    ->orWhereNull('kampus_id');
            });
        }

        $records = $query->limit(100)->get();

        $catalog = [];

        foreach ($records as $record) {
            $attributes = $record->getAttributes();

            $nama = $attributes['nama']
                ?? $attributes['nama_mk']
                ?? $attributes['nama_matkul']
                ?? $attributes['nama_mata_kuliah']
                ?? null;

            if (!is_string($nama) || trim($nama) === '') {
                continue;
            }

            $course = [
                'nama' => trim($nama),
            ];

            $optionalFields = [
                'kode',
                'kode_mk',
                'kategori',
                'kategori_kompetensi',
                'deskripsi',
                'kompetensi',
                'capaian_pembelajaran',
                'sks',
                'semester',
                'prasyarat',
            ];

            foreach ($optionalFields as $field) {
                if (
                    isset($attributes[$field]) &&
                    is_scalar($attributes[$field]) &&
                    trim((string) $attributes[$field]) !== ''
                ) {
                    $course[$field] = $attributes[$field];
                }
            }

            $catalog[] = $course;
        }

        return $catalog;
    }

    /**
     * Meminta Gemini menghasilkan 20 pertanyaan.
     * Setiap kategori wajib memiliki empat pertanyaan.
     */
    private function generateQuestions(array $courses): array
    {
        $apiKey = config('services.gemini.key')
            ?: env('GEMINI_API_KEY');

        $model = config('services.gemini.model')
            ?: env('GEMINI_MODEL', 'gemini-3.5-flash-lite');

        $model = preg_replace(
            '/^models\//',
            '',
            trim((string) $model)
        );

        if (!$apiKey) {
            throw new RuntimeException(
                'GEMINI_API_KEY belum dikonfigurasi.'
            );
        }

        if (!$model) {
            throw new RuntimeException(
                'GEMINI_MODEL belum dikonfigurasi.'
            );
        }

        $categories = array_keys($this->categories());

        $courseJson = json_encode(
            $courses,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES |
            JSON_PRETTY_PRINT
        );

        $categoryJson = json_encode(
            $categories,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
        );

        $prompt = <<<PROMPT
Kamu adalah NARA, AI asesmen minat akademik pada platform ARAH.

Buat tepat 20 pernyataan asesmen minat akademik berdasarkan katalog mata kuliah berikut.

KATEGORI WAJIB:
{$categoryJson}

KATALOG MATA KULIAH DARI DATABASE:
{$courseJson}

ATURAN:
1. Buat tepat empat pertanyaan untuk setiap kategori, total 20.
2. Nama kategori harus persis sama dengan kategori wajib.
3. Setiap pertanyaan adalah pernyataan orang pertama untuk skala persetujuan 1 sampai 5.
4. Skala: 1 sangat tidak setuju, 2 tidak setuju, 3 netral, 4 setuju, 5 sangat setuju.
5. Gunakan bahasa Indonesia yang natural dan mudah dipahami mahasiswa.
6. Ukur minat dan preferensi, bukan pengetahuan teknis benar atau salah.
7. Variasikan pertanyaan dan hindari pengulangan.
8. Sesuaikan pertanyaan dengan katalog mata kuliah.
9. Jangan mengarang nama mata kuliah yang tidak ada pada katalog.
10. Jangan menghasilkan skor atau rekomendasi.
11. Urutkan pertanyaan berdasarkan urutan kategori.
12. Keluarkan JSON valid saja tanpa markdown.

FORMAT:
{
  "questions": [
    {
      "category": "Pemrograman",
      "question": "Saya tertarik ..."
    }
  ]
}

Objek questions harus berisi tepat 20 item.
PROMPT;

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/'
            . $model
            . ':generateContent';

        $response = Http::timeout(60)
            ->withHeaders([
                'x-goog-api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->post($url, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'maxOutputTokens' => 5000,
                ],
            ]);

        if (!$response->successful()) {
            $apiMessage = data_get(
                $response->json(),
                'error.message',
                $response->body()
            );

            throw new RuntimeException(
                'Gemini API HTTP '
                . $response->status()
                . ': '
                . (
                    is_string($apiMessage)
                        ? $apiMessage
                        : json_encode(
                            $apiMessage,
                            JSON_UNESCAPED_UNICODE
                        )
                )
            );
        }

        $parts = $response->json(
            'candidates.0.content.parts',
            []
        );

        $text = collect($parts)
            ->pluck('text')
            ->filter(fn ($part) => is_string($part))
            ->implode("\n");

        $text = trim($text);

        if ($text === '') {
            $blockReason = $response->json(
                'promptFeedback.blockReason'
            );

            throw new RuntimeException(
                'Gemini tidak mengembalikan teks pertanyaan.'
                . (
                    $blockReason
                        ? ' Block reason: ' . $blockReason
                        : ''
                )
            );
        }

        $text = preg_replace(
            '/^```(?:json)?\s*|\s*```$/i',
            '',
            $text
        );

        $decoded = json_decode($text, true);

        if (
            !is_array($decoded) ||
            !isset($decoded['questions']) ||
            !is_array($decoded['questions']) ||
            count($decoded['questions']) !== 20
        ) {
            throw new RuntimeException(
                'JSON Gemini tidak valid atau jumlah pertanyaan bukan 20.'
            );
        }

        $normalized = [];
        $counts = array_fill_keys($categories, 0);
        $uniqueQuestions = [];

        foreach ($decoded['questions'] as $item) {
            if (!is_array($item)) {
                throw new RuntimeException(
                    'Format salah satu pertanyaan Gemini tidak valid.'
                );
            }

            $category = $item['category'] ?? null;

            $question = trim(
                (string) ($item['question'] ?? '')
            );

            if (!in_array($category, $categories, true)) {
                throw new RuntimeException(
                    'Gemini menghasilkan kategori yang tidak dikenal.'
                );
            }

            if (
                mb_strlen($question) < 10 ||
                mb_strlen($question) > 500
            ) {
                throw new RuntimeException(
                    'Panjang salah satu pertanyaan Gemini tidak valid.'
                );
            }

            $uniqueKey = mb_strtolower(
                preg_replace('/\s+/', ' ', $question)
            );

            if (isset($uniqueQuestions[$uniqueKey])) {
                throw new RuntimeException(
                    'Gemini menghasilkan pertanyaan duplikat.'
                );
            }

            $uniqueQuestions[$uniqueKey] = true;
            $counts[$category]++;

            $normalized[] = [
                'category' => $category,
                'question' => $question,
            ];
        }

        foreach ($counts as $category => $count) {
            if ($count !== 4) {
                throw new RuntimeException(
                    'Gemini harus menghasilkan tepat empat pertanyaan untuk kategori '
                    . $category . '.'
                );
            }
        }

        // Pastikan urutan kategori selalu konsisten:
        // q1-q4 Pemrograman, q5-q8 UI/UX,
        // q9-q12 Data, q13-q16 Sistem, q17-q20 Bisnis.
        $ordered = [];

        foreach ($categories as $category) {
            foreach ($normalized as $question) {
                if ($question['category'] === $category) {
                    $ordered[] = $question;
                }
            }
        }

        return $ordered;
    }

    /**
     * Format groups untuk Blade assessment/index.blade.php.
     */
    private function buildGroups(array $questions): array
    {
        $definitions = $this->categories();
        $groups = [];

        foreach ($definitions as $category => $label) {
            $categoryQuestions = [];

            foreach ($questions as $item) {
                if (($item['category'] ?? null) === $category) {
                    $categoryQuestions[] = $item['question'];
                }
            }

            $groups[] = [
                $category,
                $label,
                $categoryQuestions,
            ];
        }

        return $groups;
    }

    /**
     * Validasi jawaban dan simpan hasil asesmen.
     *
     * PENTING:
     * - Tidak lagi mewajibkan assessment_payload.
     * - Kategori dihitung dari urutan soal yang sudah diurutkan NARA.
     * - Jika penyimpanan gagal, error dicatat dan tidak disamarkan
     *   sebagai redirect kembali ke halaman asesmen.
     * - Setelah sukses, arahkan browser dengan HTTP 303 ke profil minat.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        Log::info('ARAH_ASSESSMENT_SUBMIT', [
            'route' => $request->route()?->getName(),
            'user_id' => $user?->id,
            'input_keys' => array_keys(
                $request->except('_token')
            ),
            'payload_present' => $request->filled(
                'assessment_payload'
            ),
        ]);

        $mahasiswa = Mahasiswa::where(
            'user_id',
            $user->id
        )->firstOrFail();

        $rules = [];

        for ($i = 1; $i <= 20; $i++) {
            $rules['q' . $i] = [
                'required',
                'integer',
                'between:1,5',
            ];
        }

        $validator = Validator::make(
            $request->all(),
            $rules
        );

        if ($validator->fails()) {
            $failedFields = array_keys(
                $validator->failed()
            );

            Log::warning(
                'ARAH asesmen gagal validasi jawaban.',
                [
                    'user_id' => $user->id,
                    'mahasiswa_id' => $mahasiswa->id,
                    'failed_fields' => $failedFields,
                ]
            );

            return redirect()
                ->route('assessment.show')
                ->withErrors($validator)
                ->withInput()
                ->with(
                    'error',
                    'Ada jawaban yang belum valid. Periksa pertanyaan yang ditandai.'
                );
        }

        $data = $validator->validated();

        $categories = array_keys(
            $this->categories()
        );

        $totals = array_fill_keys($categories, 0);
        $counts = array_fill_keys($categories, 0);

        // Setiap empat soal dihitung untuk satu kategori.
        for ($i = 1; $i <= 20; $i++) {
            $category = $categories[
                intdiv($i - 1, 4)
            ];

            $totals[$category] += (int) $data['q' . $i];
            $counts[$category]++;
        }

        foreach ($categories as $category) {
            if ($counts[$category] !== 4) {
                throw new RuntimeException(
                    'Jumlah soal kategori '
                    . $category
                    . ' bukan empat.'
                );
            }
        }

        try {
            DB::transaction(function () use (
                $mahasiswa,
                $categories,
                $totals,
                $counts
            ) {
                // Ganti hasil asesmen lama secara atomik.
                AsesmenMinat::where(
                    'mahasiswa_id',
                    $mahasiswa->id
                )->delete();

                foreach ($categories as $category) {
                    $average = $totals[$category]
                        / $counts[$category];

                    // Skala 1-5 dikonversi menjadi 0-100.
                    $score = round(
                        (($average - 1) / 4) * 100,
                        2
                    );

                    AsesmenMinat::create([
                        'mahasiswa_id' => $mahasiswa->id,
                        'kategori' => $category,
                        'skor' => $score,
                    ]);
                }
            });
        } catch (Throwable $e) {
            Log::error(
                'ARAH gagal menyimpan hasil asesmen.',
                [
                    'user_id' => $user->id,
                    'mahasiswa_id' => $mahasiswa->id,
                    'exception' => get_class($e),
                    'error' => $e->getMessage(),
                ]
            );

            // Perlihatkan error yang sebenarnya saat local/debug.
            throw $e;
        }

        // Hapus pertanyaan tersimpan setelah hasil berhasil disimpan.
        $request->session()->forget(
            $this->questionSessionKey(
                (int) $mahasiswa->id
            )
        );

        $savedCount = AsesmenMinat::where(
            'mahasiswa_id',
            $mahasiswa->id
        )->count();

        Log::info(
            'ARAH_ASSESSMENT_SAVED_REDIRECT',
            [
                'user_id' => $user->id,
                'mahasiswa_id' => $mahasiswa->id,
                'saved_category_count' => $savedCount,
                'target_route' => 'interest.profile',
            ]
        );

        // 303 memaksa browser melakukan GET setelah POST sukses.
        return redirect()->route(
            'interest.profile',
            [],
            303
        );
    }

    /**
     * Halaman profil minat.
     * Struktur data dipertahankan agar kompatibel dengan Blade lama.
     */
    public function profile(Request $request)
    {
        Log::info('ARAH_PROFILE_HIT', [
            'user_id' => $request->user()?->id,
            'path' => $request->path(),
        ]);

        $mahasiswa = Mahasiswa::with([
            'asesmen',
            'nilai',
        ])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $asesmen = $mahasiswa->asesmen
            ->sortByDesc('skor')
            ->values();

        $tertinggi = $asesmen->first();

        $interestAverage = $mahasiswa->asesmen->avg('skor');

        $interestAverage = $interestAverage !== null
            ? round($interestAverage)
            : 0;

        $academicAbility = $mahasiswa->nilai->avg(
            'nilai_angka'
        );

        $academicAbility = $academicAbility !== null
            ? round($academicAbility)
            : 0;

        $dnaIndex = round(
            ($interestAverage + $academicAbility) / 2
        );

        $dnaProfiles = [
            'Pemrograman' => [
                'name' => 'TECH BUILDER',
                'description' =>
                    'Kamu menunjukkan ketertarikan kuat pada pengembangan teknologi, pemrograman, dan aktivitas yang membutuhkan pemecahan masalah.',
                'traits' => [
                    'Problem Solving',
                    'Technology',
                    'Development',
                ],
            ],

            'UI/UX' => [
                'name' => 'DIGITAL CREATOR',
                'description' =>
                    'Profil kamu menunjukkan ketertarikan pada desain digital, pengalaman pengguna, dan bagaimana teknologi dapat dibuat lebih mudah digunakan.',
                'traits' => [
                    'Creativity',
                    'User Experience',
                    'Digital Design',
                ],
            ],

            'Data' => [
                'name' => 'INSIGHT ANALYST',
                'description' =>
                    'Kamu cenderung tertarik memahami pola, informasi, dan pengambilan keputusan yang didukung oleh data.',
                'traits' => [
                    'Analysis',
                    'Data Thinking',
                    'Research',
                ],
            ],

            'Sistem' => [
                'name' => 'SYSTEM EXPLORER',
                'description' =>
                    'Profil kamu menunjukkan ketertarikan pada cara kerja sistem, jaringan, infrastruktur, dan hubungan antar teknologi.',
                'traits' => [
                    'Systems Thinking',
                    'Technology',
                    'Infrastructure',
                ],
            ],

            'Bisnis' => [
                'name' => 'TECH STRATEGIST',
                'description' =>
                    'Kamu menunjukkan ketertarikan pada bagaimana teknologi dapat diterapkan untuk mendukung proses bisnis, perencanaan, dan pengambilan keputusan.',
                'traits' => [
                    'Strategy',
                    'Business',
                    'Technology',
                ],
            ],
        ];

        $dnaProfile = $dnaProfiles[
            $tertinggi->kategori ?? 'Pemrograman'
        ] ?? $dnaProfiles['Pemrograman'];

        $kategoriOrder = [
            'Pemrograman',
            'UI/UX',
            'Data',
            'Sistem',
            'Bisnis',
        ];

        $radarScores = [];

        foreach ($kategoriOrder as $kategori) {
            $item = $mahasiswa->asesmen
                ->firstWhere('kategori', $kategori);

            $radarScores[] = [
                'kategori' => $kategori,
                'skor' => $item
                    ? (float) $item->skor
                    : 0,
            ];
        }

        $naraInsight =
            'Berdasarkan hasil asesmen, bidang yang paling '
            . 'menonjol dalam profil kamu adalah '
            . ($tertinggi->kategori ?? 'belum tersedia')
            . ' dengan skor '
            . (
                $tertinggi
                    ? round($tertinggi->skor) . '%'
                    : '0%'
            )
            . '.';

        if ($asesmen->count() >= 2) {
            $kedua = $asesmen->get(1);

            $naraInsight .=
                ' Minat berikutnya terlihat pada bidang '
                . $kedua->kategori
                . ' dengan skor '
                . round($kedua->skor)
                . '%.';
        }

        $naraInsight .=
            ' Profil ini akan digunakan ARAH untuk membantu '
            . 'menentukan rekomendasi mata kuliah yang lebih '
            . 'sesuai dengan minat dan kemampuan akademikmu.';

        return view(
            'interest.profile',
            compact(
                'mahasiswa',
                'asesmen',
                'tertinggi',
                'radarScores',
                'naraInsight',
                'interestAverage',
                'academicAbility',
                'dnaIndex',
                'dnaProfile'
            )
        );
    }
}