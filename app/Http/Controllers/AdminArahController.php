<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Kampus;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;

class AdminArahController extends Controller
{
    public function index(Request $request)
    {
        $admin = $request->user();

        $totalKampus = Kampus::count();

        $totalMahasiswa = Mahasiswa::count();

        $totalDosen = Dosen::count();

        $totalAdminKampus = User::where('role', 'admin_kampus')->count();

        return view('admin-arah.dashboard', compact(
            'admin',
            'totalKampus',
            'totalMahasiswa',
            'totalDosen',
            'totalAdminKampus'
        ));
    }
}