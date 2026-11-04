<?php

namespace Database\Seeders;

use App\Models\Kampus;
use App\Models\Prodi;
use Illuminate\Database\Seeder;

class ProdiSeeder extends Seeder
{
    public function run(): void
    {
        // Daftar program studi. Tambahkan yang lain di baris berikutnya bila perlu.
        $daftar = [
            'Sistem dan Teknologi Informasi',
            'Teknik Elektro',
            'Teknik Mesin',
            'Teknik Informatika',
            'Teknologi Rekayasa Mekatronika',
        ];

        // Daftar yang sama dibuat untuk setiap kampus; aman dijalankan berulang
        foreach (Kampus::all() as $kampus) {
            foreach ($daftar as $nama) {
                Prodi::firstOrCreate(['kampus_id' => $kampus->id, 'nama' => $nama]);
            }
        }
    }
}