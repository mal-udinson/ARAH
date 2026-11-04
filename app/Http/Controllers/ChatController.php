<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ChatHistory;
use App\Models\Mahasiswa;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $messages = ChatHistory::where(
            'user_id',
            $request->user()->id
        )
        ->latest()
        ->take(30)
        ->get()
        ->reverse();

        return view('chat.index', [
            'messages' => $messages,
        ]);
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Ambil data akademik mahasiswa
        |--------------------------------------------------------------------------
        */

        $mahasiswa = Mahasiswa::with([
            'asesmen',
            'nilai',
            'rekomendasi.mataKuliah',
        ])
        ->where('user_id', $user->id)
        ->first();

        /*
        |--------------------------------------------------------------------------
        | Simpan pesan user
        |--------------------------------------------------------------------------
        */

        ChatHistory::create([
            'user_id' => $user->id,
            'role' => 'user',
            'message' => $data['message'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Buat context akademik
        |--------------------------------------------------------------------------
        */

        $context = '';

        if ($mahasiswa) {
            $context .= "DATA MAHASISWA:\n";
            $context .= "Nama: {$mahasiswa->nama}\n";
            $context .= "NIM: {$mahasiswa->nim}\n";
            $context .= "Semester: {$mahasiswa->semester}\n";
            $context .= "IPK: {$mahasiswa->ipk}\n";

            $nilaiRata = $mahasiswa->nilai->avg('nilai_angka');

            if ($nilaiRata !== null) {
                $context .= "Rata-rata nilai: "
                    . round($nilaiRata, 2)
                    . "\n";
            }

            if ($mahasiswa->asesmen->isNotEmpty()) {
                $context .= "\nHASIL ASESMEN MINAT:\n";

                foreach ($mahasiswa->asesmen as $asesmen) {
                    $context .= "- {$asesmen->kategori}: "
                        . "{$asesmen->skor}/5\n";
                }
            }

            if ($mahasiswa->rekomendasi->isNotEmpty()) {
                $context .= "\nREKOMENDASI MATA KULIAH:\n";

                foreach (
                    $mahasiswa->rekomendasi
                        ->sortByDesc('skor')
                        ->take(5) as $rekomendasi
                ) {
                    $namaMk = $rekomendasi->mataKuliah?->nama
                        ?? 'Mata Kuliah';

                    $context .= "- {$namaMk}: "
                        . "{$rekomendasi->skor}%\n";
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil riwayat percakapan
        |--------------------------------------------------------------------------
        */

        $history = ChatHistory::where(
            'user_id',
            $user->id
        )
        ->latest()
        ->take(10)
        ->get()
        ->reverse();

        /*
        |--------------------------------------------------------------------------
        | System instruction
        |--------------------------------------------------------------------------
        */

        $instructions = <<<PROMPT
Kamu adalah NARA (Your Academic Navigation Assistant),
asisten akademik pada aplikasi ARAH.

Tugas utama:
1. Membantu mahasiswa memahami kondisi akademiknya.
2. Menjelaskan hasil rekomendasi mata kuliah.
3. Membantu memahami minat dan kemampuan akademik.
4. Membantu menyusun rencana studi.
5. Menjawab secara natural, ramah, jelas, dan ringkas.
6. Gunakan data mahasiswa yang diberikan sebagai konteks.
7. Jangan mengarang data yang tidak tersedia.
8. Jangan mengarang nilai, IPK, mata kuliah, atau hasil asesmen.
9. Jika data tidak tersedia, katakan bahwa data tersebut belum tersedia.
10. Jangan berpura-pura menjadi Dosen PA.
11. Untuk keputusan akademik penting, sarankan diskusi dengan Dosen PA.
12. Gunakan bahasa Indonesia yang mudah dipahami mahasiswa.

DATA AKADEMIK MAHASISWA:

{$context}
PROMPT;

        /*
        |--------------------------------------------------------------------------
        | Buat contents untuk Gemini
        |--------------------------------------------------------------------------
        |
        | Gemini memakai:
        | user  -> user
        | model -> assistant dari database
        |
        */

        $contents = [];

        foreach ($history as $chat) {
            $contents[] = [
                'role' => $chat->role === 'assistant'
                    ? 'model'
                    : 'user',
                'parts' => [
                    [
                        'text' => $chat->message,
                    ],
                ],
            ];
        }

        try {
            /*
            |--------------------------------------------------------------------------
            | Request ke Gemini
            |--------------------------------------------------------------------------
            */

            $model = config(
                'services.gemini.model',
                'gemini-2.5-flash-lite'
            );

            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

            $response = Http::withHeaders([
                'x-goog-api-key' => config('services.gemini.key'),
                'Content-Type' => 'application/json',
            ])
            ->acceptJson()
            ->timeout(60)
            ->post($url, [
                'systemInstruction' => [
                    'parts' => [
                        [
                            'text' => $instructions,
                        ],
                    ],
                ],

                'contents' => $contents,

                'generationConfig' => [
                    'maxOutputTokens' => 300,
                    'temperature' => 0.7,
                ],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Kalau Gemini mengembalikan error
            |--------------------------------------------------------------------------
            */

            if ($response->failed()) {
                Log::error('NARA Gemini API Error', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);

                return response()->json([
                    'message' => 'NARA gagal terhubung ke layanan Gemini.',
                ], 500);
            }

            /*
            |--------------------------------------------------------------------------
            | Ambil jawaban Gemini
            |--------------------------------------------------------------------------
            */

            $result = $response->json();

            $reply = '';

            foreach ($result['candidates'] ?? [] as $candidate) {
                foreach (
                    $candidate['content']['parts'] ?? [] as $part
                ) {
                    if (isset($part['text'])) {
                        $reply .= $part['text'];
                    }
                }
            }

            if (trim($reply) === '') {
                $reply = 'Maaf, NARA belum mendapatkan jawaban dari Gemini.';
            }

            /*
            |--------------------------------------------------------------------------
            | Simpan jawaban NARA
            |--------------------------------------------------------------------------
            */

            ChatHistory::create([
                'user_id' => $user->id,
                'role' => 'assistant',
                'message' => $reply,
            ]);

            return response()->json([
                'reply' => $reply,
            ]);

        } catch (\Throwable $e) {

            Log::error('NARA Gemini Exception', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat menghubungi NARA.',
            ], 500);
        }
    }
}