<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kampus;

class KampusSeeder extends Seeder
{
    public function run(): void
    {
        $kampus = [
            [
                'nama' => 'Universitas Muhammadiyah Prof. Dr. Hamka',
                'kode' => 'UHAMKA',
                'email' => 'info@uhamka.ac.id',
                'alamat' => 'Jakarta, Indonesia',
                'status' => true,
            ],
            [
                'nama' => 'Universitas Indonesia',
                'kode' => 'UI',
                'email' => null,
                'alamat' => 'Depok, Jawa Barat',
                'status' => true,
            ],
            [
                'nama' => 'BINUS University',
                'kode' => 'BINUS',
                'email' => null,
                'alamat' => 'Jakarta, Indonesia',
                'status' => true,
            ],
            [
                'nama' => 'Universitas Brawijaya',
                'kode' => 'UB',
                'email' => null,
                'alamat' => 'Malang, Jawa Timur',
                'status' => true,
            ],
            [
                'nama' => 'Telkom University',
                'kode' => 'TEL-U',
                'email' => null,
                'alamat' => 'Bandung, Jawa Barat',
                'status' => true,
            ],
        ];

        foreach ($kampus as $data) {
            Kampus::updateOrCreate(
                ['kode' => $data['kode']],
                $data
            );
        }
    }
}