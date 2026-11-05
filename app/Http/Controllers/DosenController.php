<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Rekomendasi;
use Illuminate\Http\Request;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        $dosen = Dosen::with([
            'kampus',
            'mahasiswa.user',
            'mahasiswa.nilai.mataKuliah',
            'mahasiswa.asesmen',
            'mahasiswa.rekomendasi.mataKuliah',
        ])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $mahasiswa = $dosen->mahasiswa;

        $totalMahasiswa = $mahasiswa->count();

        $rataIpk = $mahasiswa->avg('ipk');

        $totalRekomendasi = $mahasiswa->sum(
            fn ($item) => $item->rekomendasi->count()
        );

        return view('dosen.dashboard', compact(
            'dosen',
            'mahasiswa',
            'totalMahasiswa',
            'rataIpk',
            'totalRekomendasi'
        ));
    }

    public function showMahasiswa(
        Request $request,
        int $mahasiswaId
    ) {
        $dosen = Dosen::where(
            'user_id',
            $request->user()->id
        )->firstOrFail();

        $mahasiswa = $dosen->mahasiswa()
            ->with([
                'user',
                'kampus',
                'dosenPa',
                'nilai.mataKuliah',
                'asesmen',
                'rekomendasi.mataKuliah',
            ])
            ->where('mahasiswa.id', $mahasiswaId)
            ->firstOrFail();

        return view(
            'dosen.mahasiswa.show',
            compact('dosen', 'mahasiswa')
        );
    }

    /**
     * Dosen PA menyetujui, meminta revisi, atau menolak rekomendasi
     * mahasiswa bimbingannya. Setiap keputusan dicatat di audit_logs.
     */
    public function updateRekomendasi(
        Request $request,
        int $rekomendasiId
    ) {
        $dosen = Dosen::where(
            'user_id',
            $request->user()->id
        )->firstOrFail();

        $rekomendasi = Rekomendasi::findOrFail($rekomendasiId);

        // Pastikan rekomendasi ini milik mahasiswa bimbingan dosen ini.
        $mahasiswa = Mahasiswa::where('id', $rekomendasi->mahasiswa_id)
            ->where('dosen_pa_id', $dosen->id)
            ->firstOrFail();

        $validated = $request->validate([
            'status' => ['required', 'in:disetujui,revisi,ditolak'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $catatan = trim((string) ($validated['catatan'] ?? ''));

        if (
            in_array($validated['status'], ['revisi', 'ditolak'], true)
            && $catatan === ''
        ) {
            return back()->with(
                'error',
                'Catatan wajib diisi untuk keputusan Revisi atau Tolak.'
            );
        }

        $statusLama = $rekomendasi->status;

        $rekomendasi->update([
            'status' => $validated['status'],
        ]);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'kampus_id' => $dosen->kampus_id,
            'role' => 'dosen',
            'action' => 'REKOMENDASI_KEPUTUSAN',
            'module' => 'rekomendasi',
            'description' => "Dosen PA mengubah status rekomendasi #{$rekomendasi->id} "
                . "untuk mahasiswa #{$mahasiswa->id} dari {$statusLama} menjadi {$validated['status']}."
                . ($catatan !== '' ? " Catatan: {$catatan}" : ''),
            'method' => $request->method(),
            'route_name' => $request->route()?->getName(),
            'route_path' => $request->path(),
            'target_type' => 'rekomendasi',
            'target_id' => (string) $rekomendasi->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()
            ->route('dosen.mahasiswa.show', $mahasiswa->id)
            ->with('success', 'Keputusan rekomendasi berhasil disimpan.');
    }
}
