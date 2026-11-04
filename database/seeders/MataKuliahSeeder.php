<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MataKuliahSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Hapus data dummy lama ARAH
        |--------------------------------------------------------------------------
        */

        $dummyCodes = [
            'TI301',
            'TI302',
            'TI303',
            'TI304',
            'TI305',
            'TI306',
            'TI307',
            'TI308',
            'TI309',
            'TI310',
            'TI311',
            'TI312',
            'TI313',
            'TI314',
            'TI315',
        ];

        $dummyIds = DB::table('mata_kuliah')
            ->whereIn('kode', $dummyCodes)
            ->pluck('id');

        if ($dummyIds->isNotEmpty()) {

            DB::table('rekomendasi')
                ->whereIn('mata_kuliah_id', $dummyIds)
                ->delete();

            DB::table('nilai')
                ->whereIn('mata_kuliah_id', $dummyIds)
                ->delete();

            DB::table('mata_kuliah')
                ->whereIn('id', $dummyIds)
                ->delete();
        }


        /*
        |--------------------------------------------------------------------------
        | DATA MATA KULIAH
        |--------------------------------------------------------------------------
        */

        $mataKuliah = [

            // =====================================================
            // SEMESTER 4
            // =====================================================

            [
                'kode' => '03045047',
                'nama' => 'Analisis & Peranc. Sistem Info',
                'kategori' => 'Sistem',
                'sks' => 3,
                'semester' => 4,
                'prasyarat' => null,
            ],

            [
                'kode' => '03045049',
                'nama' => 'Desain UI/X',
                'kategori' => 'UI/UX',
                'sks' => 2,
                'semester' => 4,
                'prasyarat' => null,
            ],

            [
                'kode' => '03045048',
                'nama' => 'Internet Of Things (IOT)',
                'kategori' => 'Sistem',
                'sks' => 3,
                'semester' => 4,
                'prasyarat' => null,
            ],

            [
                'kode' => '03045034',
                'nama' => 'Keamanan Info. dan Jaringan',
                'kategori' => 'Sistem',
                'sks' => 3,
                'semester' => 4,
                'prasyarat' => null,
            ],

            [
                'kode' => '03045007',
                'nama' => 'Muamalah',
                'kategori' => 'Umum',
                'sks' => 2,
                'semester' => 4,
                'prasyarat' => null,
            ],

            [
                'kode' => '03045032',
                'nama' => 'Pemrograman Web',
                'kategori' => 'Pemrograman',
                'sks' => 2,
                'semester' => 4,
                'prasyarat' => null,
            ],

            [
                'kode' => '03045033',
                'nama' => 'Praktikum Pemrograman Web',
                'kategori' => 'Pemrograman',
                'sks' => 1,
                'semester' => 4,
                'prasyarat' => null,
            ],

            [
                'kode' => '03045045',
                'nama' => 'Sistem Terdistribusi',
                'kategori' => 'Sistem',
                'sks' => 3,
                'semester' => 4,
                'prasyarat' => null,
            ],

            [
                'kode' => '03045046',
                'nama' => 'Sistem Informasi Akuntansi',
                'kategori' => 'Bisnis',
                'sks' => 3,
                'semester' => 4,
                'prasyarat' => null,
            ],


            // =====================================================
            // SEMESTER 5
            // =====================================================

            [
                'kode' => '03045008',
                'nama' => 'Kemuhammadiyahan',
                'kategori' => 'Umum',
                'sks' => 2,
                'semester' => 5,
                'prasyarat' => 'Tidak Ada',
            ],

            [
                'kode' => '03045050',
                'nama' => 'Pengembangan Sistem Informasi Manajemen',
                'kategori' => 'Bisnis',
                'sks' => 3,
                'semester' => 5,
                'prasyarat' => 'Manajemen Informasi Perusahaan',
            ],

            [
                'kode' => '03045051',
                'nama' => 'Mobile Application',
                'kategori' => 'Pemrograman',
                'sks' => 2,
                'semester' => 5,
                'prasyarat' => 'Pemrograman Web',
            ],

            [
                'kode' => '03045052',
                'nama' => 'Praktikum Mobile Application',
                'kategori' => 'Pemrograman',
                'sks' => 1,
                'semester' => 5,
                'prasyarat' => 'Praktikum Pemrograman Web',
            ],

            [
                'kode' => '03045053',
                'nama' => 'Perancangan Infrastruktur Teknologi Informasi',
                'kategori' => 'Sistem',
                'sks' => 2,
                'semester' => 5,
                'prasyarat' => 'Jaringan Komputer dan Komunikasi Data',
            ],

            [
                'kode' => '03045016',
                'nama' => 'Manajemen Proyek dan Perubahan',
                'kategori' => 'Bisnis',
                'sks' => 3,
                'semester' => 5,
                'prasyarat' => 'Metodologi Pengembangan Sistem Informasi',
            ],

            [
                'kode' => '03045098',
                'nama' => 'Pancasila dan Kewarganegaraan',
                'kategori' => 'Umum',
                'sks' => 3,
                'semester' => 5,
                'prasyarat' => 'Tidak Ada',
            ],

            [
                'kode' => '03045097',
                'nama' => 'Ketrampilan Bahasa Jepang',
                'kategori' => 'Bahasa',
                'sks' => 2,
                'semester' => 5,
                'prasyarat' => 'Tidak Ada',
            ],

            [
                'kode' => '03045104',
                'nama' => 'Ketrampilan Berbicara Bahasa Jepang',
                'kategori' => 'Bahasa',
                'sks' => 2,
                'semester' => 5,
                'prasyarat' => 'Tidak Ada',
            ],
        ];


        /*
        |--------------------------------------------------------------------------
        | INSERT / UPDATE
        |--------------------------------------------------------------------------
        */

        foreach ($mataKuliah as $mk) {

            DB::table('mata_kuliah')->updateOrInsert(
                [
                    'kode' => $mk['kode'],
                ],
                [
                    'nama' => $mk['nama'],
                    'kategori' => $mk['kategori'],
                    'sks' => $mk['sks'],
                    'semester' => $mk['semester'],
                    'prasyarat' => $mk['prasyarat'],
                ]
            );
        }
    }
}