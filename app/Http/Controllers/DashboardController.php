<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $m = Mahasiswa::with([
            'asesmen',
            'nilai',
            'rekomendasi.mataKuliah',
            'dosenPa',
            'dosenPa.user',
            'dosenPa.kampus',
        ])
        ->where('user_id', $request->user()->id)
        ->firstOrFail();

        // Rata-rata nilai
        $rataNilai = $m->nilai->avg('nilai_angka');

        $rataNilai = $rataNilai !== null
            ? round($rataNilai, 2)
            : null;

        // Data minat
        $minat = $m->asesmen
            ->sortByDesc('skor')
            ->values();

        // Rekomendasi
        $rekomendasi = $m->rekomendasi
            ->sortByDesc('skor')
            ->values();

        // 3 rekomendasi untuk dashboard
        $topRecommendations = $rekomendasi->take(3);

        // Minat tertinggi
        $minatTertinggi = $minat->first();

        // Insight NARA
        if ($topRecommendations->isNotEmpty()) {

            $first = $topRecommendations->first();

            $naraInsight = $first->alasan
                ?? 'NARA telah menganalisis profil akademik kamu.';

        } elseif ($minatTertinggi) {

            $naraInsight =
                'Minat tertinggi kamu berada pada bidang '
                . $minatTertinggi->kategori
                . ' dengan skor '
                . round($minatTertinggi->skor)
                . '%.';

        } else {

            $naraInsight =
                'Lengkapi asesmen minat agar NARA dapat memberikan insight akademik personal.';
        }

        return view('dashboard.index', compact(
            'm',
            'rataNilai',
            'minat',
            'minatTertinggi',
            'rekomendasi',
            'topRecommendations',
            'naraInsight'
        ));
    }
}