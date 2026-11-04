<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;

class AdminKampusController extends Controller
{
    public function index(Request $request)
    {
        $admin = $request->user();

        $kampus = $admin->kampus;

        if (!$kampus) {
            abort(403, 'Admin belum memiliki kampus.');
        }

        $totalMahasiswa = Mahasiswa::where('kampus_id', $kampus->id)->count();

        $totalDosen = Dosen::where('kampus_id', $kampus->id)->count();

        $totalUser = User::where('kampus_id', $kampus->id)
            ->whereIn('role', ['mahasiswa', 'dosen', 'admin_kampus'])
            ->count();

        return view('admin-kampus.dashboard', compact(
            'admin',
            'kampus',
            'totalMahasiswa',
            'totalDosen',
            'totalUser'
        ));
    }
}