<?php

namespace App\Http\Controllers;

use App\Models\Kampus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminArahAdminKampusController extends Controller
{
    public function index()
    {
        $adminKampus = User::with('kampus')
            ->where('role', 'admin_kampus')
            ->orderBy('name')
            ->get();

        return view(
            'admin-arah.admin-kampus.index',
            compact('adminKampus')
        );
    }

    public function create()
    {
        $kampus = Kampus::where('status', true)
            ->orderBy('nama')
            ->get();

        return view(
            'admin-arah.admin-kampus.create',
            compact('kampus')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],

            'password' => [
                'required',
                'string',
                'min:8',
            ],

            'kampus_id' => [
                'required',
                'integer',
                Rule::exists('kampus', 'id')
                    ->where(fn ($query) => $query->where('status', true)),
            ],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin_kampus',
            'kampus_id' => $validated['kampus_id'],
        ]);

        return redirect()
            ->route('admin-arah.admin-kampus.index')
            ->with('success', 'Admin Kampus berhasil ditambahkan.');
    }

    public function edit(int $adminId)
    {
        $adminKampus = User::where('role', 'admin_kampus')
            ->where('id', $adminId)
            ->firstOrFail();

        $kampus = Kampus::where('status', true)
            ->orderBy('nama')
            ->get();

        return view(
            'admin-arah.admin-kampus.edit',
            compact('adminKampus', 'kampus')
        );
    }

    public function update(Request $request, int $adminId)
    {
        $adminKampus = User::where('role', 'admin_kampus')
            ->where('id', $adminId)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($adminKampus->id),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
            ],

            'kampus_id' => [
                'required',
                'integer',
                Rule::exists('kampus', 'id')
                    ->where(fn ($query) => $query->where('status', true)),
            ],
        ]);

        $adminKampus->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'kampus_id' => $validated['kampus_id'],
        ]);

        if (!empty($validated['password'])) {
            $adminKampus->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        return redirect()
            ->route('admin-arah.admin-kampus.index')
            ->with('success', 'Data Admin Kampus berhasil diperbarui.');
    }

    public function destroy(int $adminId)
    {
        $adminKampus = User::where('role', 'admin_kampus')
            ->where('id', $adminId)
            ->firstOrFail();

        $adminKampus->delete();

        return redirect()
            ->route('admin-arah.admin-kampus.index')
            ->with('success', 'Admin Kampus berhasil dihapus.');
    }

    public function toggleStatus(int $adminId)
{
    $adminKampus = User::where('role', 'admin_kampus')
        ->where('id', $adminId)
        ->firstOrFail();

    $adminKampus->update([
        'is_active' => !$adminKampus->is_active,
    ]);

    $status = $adminKampus->is_active
        ? 'diaktifkan'
        : 'dinonaktifkan';

    return redirect()
        ->route('admin-arah.admin-kampus.index')
        ->with(
            'success',
            'Akun Admin Kampus berhasil ' . $status . '.'
        );
}
}