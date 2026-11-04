<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Kampus;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;

class AdminArahMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $totalKampus = Kampus::count();

        $totalMahasiswa = Mahasiswa::count();

        $totalDosen = Dosen::count();

        $totalAdminKampus = User::where('role', 'admin_kampus')->count();

        $mahasiswaAsesmen = Mahasiswa::has('asesmen')->count();

        $mahasiswaRekomendasi = Mahasiswa::has('rekomendasi')->count();

        $persentaseAsesmen = $totalMahasiswa > 0
            ? round(($mahasiswaAsesmen / $totalMahasiswa) * 100)
            : 0;

        $persentaseRekomendasi = $totalMahasiswa > 0
            ? round(($mahasiswaRekomendasi / $totalMahasiswa) * 100)
            : 0;

        $mahasiswaPerKampus = Mahasiswa::selectRaw(
            'kampus_id, COUNT(*) as total'
        )
            ->groupBy('kampus_id')
            ->pluck('total', 'kampus_id');

        $dosenPerKampus = Dosen::selectRaw(
            'kampus_id, COUNT(*) as total'
        )
            ->groupBy('kampus_id')
            ->pluck('total', 'kampus_id');

        $kampusStats = Kampus::orderBy('nama')
            ->get()
            ->map(function ($kampus) use ($mahasiswaPerKampus, $dosenPerKampus) {
                return [
                    'id' => $kampus->id,
                    'nama' => $kampus->nama,
                    'kode' => $kampus->kode,
                    'status' => (bool) $kampus->status,
                    'mahasiswa' => (int) ($mahasiswaPerKampus[$kampus->id] ?? 0),
                    'dosen' => (int) ($dosenPerKampus[$kampus->id] ?? 0),
                ];
            });

        $akunTerbaru = User::with('kampus')
            ->whereIn('role', [
                'mahasiswa',
                'dosen',
                'admin_kampus',
            ])
            ->latest()
            ->take(8)
            ->get();

        return view(
            'admin-arah.monitoring.index',
            compact(
                'totalKampus',
                'totalMahasiswa',
                'totalDosen',
                'totalAdminKampus',
                'mahasiswaAsesmen',
                'mahasiswaRekomendasi',
                'persentaseAsesmen',
                'persentaseRekomendasi',
                'kampusStats',
                'akunTerbaru'
            )
        );
    }
}