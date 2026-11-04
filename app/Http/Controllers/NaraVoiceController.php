<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class NaraVoiceController extends Controller
{
    public function index(Request $request)
    {
        $mahasiswa = Mahasiswa::with([
            'dosenPa',
            'asesmen',
            'nilai.mataKuliah',
            'rekomendasi.mataKuliah',
        ])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return view('nara.voice', compact('mahasiswa'));
    }

    public function session(Request $request)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA MAHASISWA
        |--------------------------------------------------------------------------
        */

        $mahasiswa = Mahasiswa::with([
            'dosenPa',
            'asesmen',
            'nilai.mataKuliah',
            'rekomendasi.mataKuliah',
        ])
            ->where('user_id', $user->id)
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | GEMINI API KEY
        |--------------------------------------------------------------------------
        */

        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'error' => 'GEMINI_API_KEY belum dikonfigurasi.'
            ], 500);
        }

        /*
        |--------------------------------------------------------------------------
        | PROFIL MINAT
        |--------------------------------------------------------------------------
        */

        $minat = $mahasiswa->asesmen
            ->sortByDesc('skor')
            ->take(5)
            ->map(function ($item) {
                return $item->kategori
                    . ': '
                    . round($item->skor)
                    . '%';
            })
            ->implode(', ');

        /*
        |--------------------------------------------------------------------------
        | RIWAYAT NILAI
        |--------------------------------------------------------------------------
        */

        $nilai = $mahasiswa->nilai
            ->sortByDesc('nilai_angka')
            ->take(8)
            ->map(function ($item) {
                return ($item->mataKuliah->nama ?? 'Mata Kuliah')
                    . ': '
                    . ($item->nilai_angka ?? '-');
            })
            ->implode(', ');

        /*
        |--------------------------------------------------------------------------
        | REKOMENDASI
        |--------------------------------------------------------------------------
        */

        $rekomendasi = $mahasiswa->rekomendasi
            ->sortByDesc('skor')
            ->take(5)
            ->map(function ($item) {
                return ($item->mataKuliah->nama ?? 'Mata Kuliah')
                    . ': '
                    . round($item->skor)
                    . '%';
            })
            ->implode(', ');

        /*
        |--------------------------------------------------------------------------
        | DOSEN PA
        |--------------------------------------------------------------------------
        */

        $dosenPa = $mahasiswa->dosenPa?->nama
            ?? 'Belum ditentukan';

        /*
        |--------------------------------------------------------------------------
        | SYSTEM INSTRUCTION NARA
        |--------------------------------------------------------------------------
        */

        $instructions = <<<TEXT
Kamu adalah NARA, asisten akademik dalam platform ARAH.

Kamu sedang berbicara langsung dengan mahasiswa melalui suara.

Gunakan bahasa Indonesia yang natural, santai, ramah, dan jelas.
Jawaban singkat, relevan, dan terdengar seperti percakapan manusia.
Jangan terlalu panjang kecuali mahasiswa meminta penjelasan detail.

Jangan mengarang data akademik mahasiswa.
Gunakan hanya data yang tersedia di bawah.
Kalau informasi tidak tersedia, katakan dengan jujur.

Data mahasiswa:

Nama: {$mahasiswa->nama}
NIM: {$mahasiswa->nim}
Semester: {$mahasiswa->semester}
IPK: {$mahasiswa->ipk}
Dosen PA: {$dosenPa}

Profil minat:
{$minat}

Riwayat nilai:
{$nilai}

Rekomendasi mata kuliah:
{$rekomendasi}

Tugas kamu:
- membantu mahasiswa memahami kondisi akademiknya;
- menjelaskan hasil asesmen;
- menjelaskan rekomendasi ARAH;
- membantu memahami pilihan mata kuliah;
- memberikan saran akademik berdasarkan data;
- berbicara secara natural melalui suara.

Jangan mengarang informasi.
Jangan mengaku sebagai dosen atau pihak kampus.

Untuk keputusan akademik final, arahkan mahasiswa untuk berkonsultasi
dengan Dosen Pembimbing Akademik.
TEXT;

        /*
        |--------------------------------------------------------------------------
        | EPHEMERAL TOKEN
        |--------------------------------------------------------------------------
        |
        | Sengaja hanya membuat token dasar.
        |
        | Konfigurasi Live API dilakukan oleh SDK
        | di browser.
        |
        |--------------------------------------------------------------------------
        */

        $expireTime = now('UTC')
            ->addMinutes(30)
            ->toIso8601String();

        $newSessionExpireTime = now('UTC')
            ->addMinute()
            ->toIso8601String();

        $payload = [
            'uses' => 1,

            'expireTime' =>
                $expireTime,

            'newSessionExpireTime' =>
                $newSessionExpireTime,
        ];

        try {

            $response = Http::timeout(30)
                ->withHeaders([
                    'x-goog-api-key' =>
                        $apiKey,

                    'Content-Type' =>
                        'application/json',

                    'Accept' =>
                        'application/json',
                ])
                ->post(
                    'https://generativelanguage.googleapis.com/v1beta/auth_tokens',
                    $payload
                );

            /*
            |--------------------------------------------------------------------------
            | ERROR DARI GEMINI
            |--------------------------------------------------------------------------
            */

            if (!$response->successful()) {

                return response()->json([
                    'error' =>
                        'Gagal membuat token Gemini Live.',

                    'http_status' =>
                        $response->status(),

                    'details' =>
                        $response->json()
                        ?? $response->body(),
                ], $response->status());
            }

            /*
            |--------------------------------------------------------------------------
            | TOKEN
            |--------------------------------------------------------------------------
            */

            $token =
                $response->json('name');

            if (!$token) {

                return response()->json([
                    'error' =>
                        'Token Gemini Live tidak ditemukan.',

                    'details' =>
                        $response->json(),
                ], 500);
            }

            /*
            |--------------------------------------------------------------------------
            | TOKEN + DATA NARA
            |--------------------------------------------------------------------------
            */

            return response()->json([
                'token' =>
                    $token,

                'model' =>
                    'gemini-3.8-live',

                'instructions' =>
                    $instructions,
            ]);

        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'error' =>
                    'Request ke Gemini gagal.',

                'details' =>
                    $e->getMessage(),
            ], 500);
        }
    }
}