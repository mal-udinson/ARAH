<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminKampusMahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $kampusId = $request->user()->kampus_id;

        $mahasiswa = Mahasiswa::with(['user', 'dosenPa'])
            ->where('kampus_id', $kampusId)
            ->orderBy('nama')
            ->get();

        return view('admin-kampus.mahasiswa.index', compact('mahasiswa'));
    }

    public function create(Request $request)
    {
        $kampusId = $request->user()->kampus_id;

        $dosen = Dosen::where('kampus_id', $kampusId)
            ->orderBy('nama')
            ->get();

        return view('admin-kampus.mahasiswa.create', compact('dosen'));
    }

    public function store(Request $request)
    {
        $kampusId = $request->user()->kampus_id;

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nim' => [
                'required',
                'string',
                'max:50',
                Rule::unique('mahasiswa', 'nim'),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'password' => ['required', 'string', 'min:8'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'ipk' => ['required', 'numeric', 'min:0', 'max:4'],
            'dosen_pa_id' => [
                'nullable',
                Rule::exists('dosen', 'id')->where(
                    fn ($query) => $query->where('kampus_id', $kampusId)
                ),
            ],
        ]);

        DB::transaction(function () use ($validated, $kampusId) {
            $user = User::create([
                'name' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'mahasiswa',
                'kampus_id' => $kampusId,
            ]);

            Mahasiswa::create([
                'user_id' => $user->id,
                'kampus_id' => $kampusId,
                'nim' => $validated['nim'],
                'nama' => $validated['nama'],
                'semester' => $validated['semester'],
                'ipk' => $validated['ipk'],
                'dosen_pa_id' => $validated['dosen_pa_id'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin-kampus.mahasiswa.index')
            ->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function edit(Request $request, int $mahasiswaId)
    {
        $kampusId = $request->user()->kampus_id;

        $mahasiswa = Mahasiswa::with('user')
            ->where('kampus_id', $kampusId)
            ->where('id', $mahasiswaId)
            ->firstOrFail();

        $dosen = Dosen::where('kampus_id', $kampusId)
            ->orderBy('nama')
            ->get();

        return view('admin-kampus.mahasiswa.edit', compact(
            'mahasiswa',
            'dosen'
        ));
    }

    public function update(Request $request, int $mahasiswaId)
    {
        $kampusId = $request->user()->kampus_id;

        $mahasiswa = Mahasiswa::with('user')
            ->where('kampus_id', $kampusId)
            ->where('id', $mahasiswaId)
            ->firstOrFail();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nim' => [
                'required',
                'string',
                'max:50',
                Rule::unique('mahasiswa', 'nim')->ignore($mahasiswa->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($mahasiswa->user_id),
            ],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'ipk' => ['required', 'numeric', 'min:0', 'max:4'],
            'dosen_pa_id' => [
                'nullable',
                Rule::exists('dosen', 'id')->where(
                    fn ($query) => $query->where('kampus_id', $kampusId)
                ),
            ],
        ]);

        DB::transaction(function () use ($validated, $mahasiswa) {
            $mahasiswa->update([
                'nama' => $validated['nama'],
                'nim' => $validated['nim'],
                'semester' => $validated['semester'],
                'ipk' => $validated['ipk'],
                'dosen_pa_id' => $validated['dosen_pa_id'] ?? null,
            ]);

            $mahasiswa->user->update([
                'name' => $validated['nama'],
                'email' => $validated['email'],
            ]);
        });

        return redirect()
            ->route('admin-kampus.mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroy(Request $request, int $mahasiswaId)
    {
        $kampusId = $request->user()->kampus_id;

        $mahasiswa = Mahasiswa::where('kampus_id', $kampusId)
            ->where('id', $mahasiswaId)
            ->firstOrFail();

        $user = $mahasiswa->user;

        DB::transaction(function () use ($mahasiswa, $user) {
            $mahasiswa->delete();

            if ($user) {
                $user->delete();
            }
        });

        return redirect()
            ->route('admin-kampus.mahasiswa.index')
            ->with('success', 'Mahasiswa berhasil dihapus.');
    }
}