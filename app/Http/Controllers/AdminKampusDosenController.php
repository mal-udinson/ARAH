<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminKampusDosenController extends Controller
{
    public function index(Request $request)
    {
        $kampusId = $request->user()->kampus_id;

        $dosen = Dosen::with('user')
            ->where('kampus_id', $kampusId)
            ->orderBy('nama')
            ->get();

        return view('admin-kampus.dosen.index', compact('dosen'));
    }

    public function create(Request $request)
    {
        return view('admin-kampus.dosen.create');
    }

    public function store(Request $request)
    {
        $kampusId = $request->user()->kampus_id;

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nip' => [
                'required',
                'string',
                'max:50',
                Rule::unique('dosen', 'nip'),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'password' => ['required', 'string', 'min:8'],
        ]);

        DB::transaction(function () use ($validated, $kampusId) {
            $user = User::create([
                'name' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'dosen',
                'kampus_id' => $kampusId,
            ]);

            Dosen::create([
                'user_id' => $user->id,
                'kampus_id' => $kampusId,
                'nip' => $validated['nip'],
                'nama' => $validated['nama'],
            ]);
        });

        return redirect()
            ->route('admin-kampus.dosen.index')
            ->with('success', 'Dosen berhasil ditambahkan.');
    }

    public function edit(Request $request, int $dosenId)
    {
        $kampusId = $request->user()->kampus_id;

        $dosen = Dosen::with('user')
            ->where('kampus_id', $kampusId)
            ->where('id', $dosenId)
            ->firstOrFail();

        return view('admin-kampus.dosen.edit', compact('dosen'));
    }

    public function update(Request $request, int $dosenId)
    {
        $kampusId = $request->user()->kampus_id;

        $dosen = Dosen::with('user')
            ->where('kampus_id', $kampusId)
            ->where('id', $dosenId)
            ->firstOrFail();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nip' => [
                'required',
                'string',
                'max:50',
                Rule::unique('dosen', 'nip')->ignore($dosen->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($dosen->user_id),
            ],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        DB::transaction(function () use ($validated, $dosen) {
            $dosen->update([
                'nama' => $validated['nama'],
                'nip' => $validated['nip'],
            ]);

            $dosen->user->update([
                'name' => $validated['nama'],
                'email' => $validated['email'],
            ]);

            if (!empty($validated['password'])) {
                $dosen->user->update([
                    'password' => Hash::make($validated['password']),
                ]);
            }
        });

        return redirect()
            ->route('admin-kampus.dosen.index')
            ->with('success', 'Data dosen berhasil diperbarui.');
    }

    public function destroy(Request $request, int $dosenId)
    {
        $kampusId = $request->user()->kampus_id;

        $dosen = Dosen::with('user')
            ->where('kampus_id', $kampusId)
            ->where('id', $dosenId)
            ->firstOrFail();

        $user = $dosen->user;

        DB::transaction(function () use ($dosen, $user) {
            $dosen->delete();

            if ($user) {
                $user->delete();
            }
        });

        return redirect()
            ->route('admin-kampus.dosen.index')
            ->with('success', 'Dosen berhasil dihapus.');
    }
}