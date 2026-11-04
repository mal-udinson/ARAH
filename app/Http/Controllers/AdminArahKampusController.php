<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Kampus;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminArahKampusController extends Controller
{
    public function index()
    {
        $kampus = Kampus::withCount([
            'users',
            'dosen',
        ])
            ->orderBy('nama')
            ->get()
            ->map(function ($item) {
                $item->total_mahasiswa = Mahasiswa::where(
                    'kampus_id',
                    $item->id
                )->count();

                return $item;
            });

        return view('admin-arah.kampus.index', compact('kampus'));
    }

    public function create()
    {
        return view('admin-arah.kampus.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kode' => [
                'required',
                'string',
                'max:20',
                'alpha_num',
                Rule::unique('kampus', 'kode'),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('kampus', 'email'),
            ],
            'alamat' => ['required', 'string', 'max:500'],
            'status' => ['required', 'boolean'],
        ]);

        Kampus::create($validated);

        return redirect()
            ->route('admin-arah.kampus.index')
            ->with('success', 'Kampus berhasil ditambahkan.');
    }

    public function show(int $kampusId)
    {
        $kampus = Kampus::findOrFail($kampusId);

        $totalMahasiswa = Mahasiswa::where(
            'kampus_id',
            $kampus->id
        )->count();

        $totalDosen = Dosen::where(
            'kampus_id',
            $kampus->id
        )->count();

        $totalAdminKampus = $kampus->users()
            ->where('role', 'admin_kampus')
            ->count();

        $totalUser = $kampus->users()->count();

        return view('admin-arah.kampus.show', compact(
            'kampus',
            'totalMahasiswa',
            'totalDosen',
            'totalAdminKampus',
            'totalUser'
        ));
    }

    public function edit(int $kampusId)
    {
        $kampus = Kampus::findOrFail($kampusId);

        return view('admin-arah.kampus.edit', compact('kampus'));
    }

    public function update(Request $request, int $kampusId)
    {
        $kampus = Kampus::findOrFail($kampusId);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kode' => [
                'required',
                'string',
                'max:20',
                'alpha_num',
                Rule::unique('kampus', 'kode')->ignore($kampus->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('kampus', 'email')->ignore($kampus->id),
            ],
            'alamat' => ['required', 'string', 'max:500'],
            'status' => ['required', 'boolean'],
        ]);

        $kampus->update($validated);

        return redirect()
            ->route('admin-arah.kampus.index')
            ->with('success', 'Data kampus berhasil diperbarui.');
    }

    public function toggleStatus(int $kampusId)
    {
        $kampus = Kampus::findOrFail($kampusId);

        $kampus->update([
            'status' => !$kampus->status,
        ]);

        $status = $kampus->status
            ? 'diaktifkan'
            : 'dinonaktifkan';

        return redirect()
            ->route('admin-arah.kampus.index')
            ->with(
                'success',
                'Kampus berhasil ' . $status . '.'
            );
    }
}