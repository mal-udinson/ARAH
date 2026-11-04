<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Kampus;
use Illuminate\Http\Request;

class AdminArahDosenController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $kampusId = $request->input('kampus_id');

        $kampus = Kampus::orderBy('nama')->get();

        $dosen = Dosen::with([
            'user',
            'kampus',
        ])
            ->withCount('mahasiswa')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nama', 'like', '%' . $search . '%')
                        ->orWhere('nip', 'like', '%' . $search . '%')
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
            'admin-arah.dosen.index',
            compact('dosen', 'kampus', 'search', 'kampusId')
        );
    }

    public function show(int $dosenId)
    {
        $dosen = Dosen::with([
            'user',
            'kampus',
            'mahasiswa.user',
            'mahasiswa.kampus',
        ])->findOrFail($dosenId);

        return view(
            'admin-arah.dosen.show',
            compact('dosen')
        );
    }
}