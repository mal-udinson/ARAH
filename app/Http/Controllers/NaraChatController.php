<?php

namespace App\Http\Controllers;

use App\Models\NaraConversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class NaraChatController extends Controller
{
    /** Halaman chat: /chat (baru) atau /chat/{id} (riwayat). */
    public function index(Request $request, ?int $id = null): View
    {
        $conversations = NaraConversation::where('user_id', $request->user()->id)
            ->orderByDesc('updated_at')->get();

        $active = $id ? $conversations->firstWhere('id', $id) : null;
        abort_if($id && ! $active, 404);

        $messages = $active ? $active->messages()->orderBy('id')->get() : collect();

        return view('chat.chat', compact('conversations', 'active', 'messages'));
    }

    /** Kirim pesan: buat percakapan baru bila belum ada, simpan, minta jawaban NARA. */
    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message'         => ['required', 'string', 'max:2000'],
            'conversation_id' => ['nullable', 'integer'],
        ]);

        $text  = trim($data['message']);
        $isNew = empty($data['conversation_id']);

        $conv = $isNew
            ? NaraConversation::create(['user_id' => $request->user()->id, 'title' => Str::limit($text, 45, '…')])
            : $this->own($request, (int) $data['conversation_id']);

        // Riwayat sebelumnya (maks. 12 pesan) sebagai konteks untuk AI
        $history = $conv->messages()->orderByDesc('id')->limit(12)->get()->reverse()
            ->map(fn ($m) => ['role' => $m->role, 'message' => $m->message])->values()->all();

        $conv->messages()->create(['role' => 'user', 'message' => $text]);

        try {
            $reply = $this->askNara($request, $history, $text);
        } catch (Throwable $e) {
            Log::error('NARA error: '.$e->getMessage());
            return response()->json(['message' => 'NARA sedang tidak bisa dihubungi.'], 500);
        }

        $conv->messages()->create(['role' => 'assistant', 'message' => $reply]);
        $conv->touch(); // naik ke paling atas di riwayat

        return response()->json([
            'reply'        => $reply,
            'conversation' => ['id' => $conv->id, 'title' => $conv->title, 'is_new' => $isNew],
        ]);
    }

    public function rename(Request $request, int $id): JsonResponse
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:60']]);
        $this->own($request, $id)->update(['title' => trim($data['title'])]);

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->own($request, $id)->delete(); // pesan ikut terhapus (cascade)

        return response()->json(['ok' => true]);
    }

    private function own(Request $request, int $id): NaraConversation
    {
        return NaraConversation::where('user_id', $request->user()->id)->findOrFail($id);
    }

    /* =====================================================================
     *  OTAK NARA (Google Gemini)
     *  Isi .env:  GEMINI_API_KEY=...   dan (opsional)  GEMINI_MODEL=gemini-2.5-flash
     * ===================================================================== */
    private function askNara(Request $request, array $history, string $text): string
    {
        $key = env('GEMINI_API_KEY') ?: env('GOOGLE_API_KEY');
        if (! $key) {
            throw new RuntimeException('GEMINI_API_KEY belum diisi di .env');
        }
        $model = env('GEMINI_MODEL', 'gemini-2.5-flash');

        // Susun riwayat percakapan (Gemini memakai peran "user" dan "model")
        $contents = [];
        foreach ($history as $h) {
            $contents[] = [
                'role'  => $h['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $h['message']]],
            ];
        }
        while ($contents && $contents[0]['role'] === 'model') {
            array_shift($contents); // percakapan harus diawali pesan user
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $text]]];

        $response = Http::withHeaders(['x-goog-api-key' => $key])
            ->timeout(45)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                'systemInstruction' => ['parts' => [['text' => $this->systemPrompt($request)]]],
                'contents'          => $contents,
                'generationConfig'  => ['temperature' => 0.7, 'maxOutputTokens' => 2048],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Gemini HTTP '.$response->status().': '.$response->body());
        }

        $reply = trim(collect($response->json('candidates.0.content.parts', []))->pluck('text')->implode(''));
        if ($reply === '') {
            throw new RuntimeException('Gemini mengembalikan balasan kosong: '.$response->body());
        }

        return $reply;
    }

    /** Kepribadian NARA + data akademik mahasiswa yang sedang login. */
    private function systemPrompt(Request $request): string
    {
        $nama = Str::of($request->user()->name)->before(' ');

        return <<<PROMPT
Kamu adalah NARA, asisten akademik AI di platform ARAH (Academic Recommendation & Assistance Hub).
Tugasmu membantu mahasiswa memahami kondisi akademik, memilih mata kuliah sesuai minat dan kemampuan, menyusun rencana studi, dan mempersiapkan perwalian dengan Dosen PA.

Gaya bicara:
- Bahasa Indonesia yang hangat, santai tapi sopan. Panggil mahasiswa dengan "kamu" dan nama depannya ({$nama}).
- Ringkas dan jelas. Gunakan paragraf pendek atau daftar singkat, jangan bertele-tele.
- Berikan alasan di balik setiap saran.

Aturan:
- Dasarkan jawaban pada data akademik di bawah. Jangan mengarang nilai, IPK, atau mata kuliah yang tidak ada di data.
- Kalau data belum ada (misalnya asesmen belum diisi), katakan apa adanya dan arahkan ke menu Asesmen atau Data akademik.
- Untuk keputusan penting (pindah jurusan, cuti, DO, beban SKS ekstrem), sarankan berdiskusi dengan Dosen PA.
- Kamu bukan psikolog atau dokter. Kalau mahasiswa tampak sangat tertekan, tanggapi dengan empati dan sarankan bicara dengan orang yang dipercaya atau layanan konseling kampus.
- Di luar topik akademik dan studi, boleh ngobrol singkat, lalu arahkan kembali dengan halus.

DATA AKADEMIK MAHASISWA SAAT INI:
{$this->studentContext($request)}
PROMPT;
    }

    /** Mengumpulkan data mahasiswa. Bagian yang gagal dibaca dilewati, chat tetap jalan. */
    private function studentContext(Request $request): string
    {
        $user = $request->user();
        $m    = $user->mahasiswa ?? null;

        if (! $m) {
            return "Nama: {$user->name}. Data akademik belum tersedia.";
        }

        $lines = [
            "Nama: {$m->nama}",
            "NIM: {$m->nim}",
            "Semester: {$m->semester}",
            'IPK: '.number_format((float) $m->ipk, 2).' (skala 4,00)',
        ];

        try {
            foreach (['minat', 'profilMinat', 'minats'] as $rel) {
                if (method_exists($m, $rel)) {
                    $minat = $m->{$rel}()->get();
                    if ($minat->isNotEmpty()) {
                        $lines[] = 'Profil minat (skor 0-100): '.$minat->map(fn ($i) => "{$i->kategori} {$i->skor}")->implode(', ');
                    }
                    break;
                }
            }
        } catch (Throwable $e) {
            Log::warning('NARA konteks minat: '.$e->getMessage());
        }

        try {
            foreach (['nilai', 'nilais', 'riwayatNilai'] as $rel) {
                if (method_exists($m, $rel)) {
                    $nilai = $m->{$rel}()->limit(40)->get();
                    if ($nilai->isNotEmpty()) {
                        $lines[] = 'Riwayat nilai: '.$nilai->map(function ($n) {
                            $mk = method_exists($n, 'mataKuliah') ? ($n->mataKuliah->nama ?? null) : null;
                            $v  = collect($n->only(['nilai_angka', 'angka', 'nilai', 'nilai_huruf', 'huruf']))->filter()->implode('/');
                            return trim(($mk ?: 'Mata kuliah').' '.$v);
                        })->implode('; ');
                    }
                    break;
                }
            }
        } catch (Throwable $e) {
            Log::warning('NARA konteks nilai: '.$e->getMessage());
        }

        try {
            $rek = $m->rekomendasi()->with('mataKuliah')->orderByDesc('skor')->limit(5)->get();
            if ($rek->isNotEmpty()) {
                $lines[] = 'Rekomendasi mata kuliah teratas dari ARAH: '.$rek->map(
                    fn ($r) => ($r->mataKuliah->nama ?? 'Mata kuliah').' (kecocokan '.round($r->skor).'%'.($r->alasan ? ', alasan: '.$r->alasan : '').')'
                )->implode('; ');
            }
        } catch (Throwable $e) {
            Log::warning('NARA konteks rekomendasi: '.$e->getMessage());
        }

        return implode("\n", $lines);
    }
}