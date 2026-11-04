<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
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
}