<?php

namespace App\Http\Controllers;

use App\Models\Kampus;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class AdminArahMahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $kampusId = $request->input('kampus_id');

        $kampus = Kampus::orderBy('nama')->get();

        $mahasiswa = Mahasiswa::with([
            'user',
            'kampus',
            'dosenPa',
        ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nama', 'like', '%' . $search . '%')
                        ->orWhere('nim', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->where('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($kampusId !== null && $kampusId !== '', function ($query) use ($kampusId) {
                $query->where('kampus_id', $kampusId);
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin-arah.mahasiswa.index',
            compact('mahasiswa', 'kampus', 'search', 'kampusId')
        );
    }

    public function show(int $mahasiswaId)
    {
        $mahasiswa = Mahasiswa::with([
            'user',
            'kampus',
            'dosenPa',
            'nilai.mataKuliah',
            'asesmen',
            'rekomendasi.mataKuliah',
        ])->findOrFail($mahasiswaId);

        return view(
            'admin-arah.mahasiswa.show',
            compact('mahasiswa')
        );
    }
}