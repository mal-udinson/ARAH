<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
class DatabaseSeeder extends Seeder {
 public function run(): void {
  $u=User::create(['name'=>'Ahmad Fauzi','email'=>'mahasiswa@arah.test','password'=>Hash::make('password')]);
  Mahasiswa::create(['user_id'=>$u->id,'nim'=>'2403045001','nama'=>'Ahmad Fauzi','semester'=>5,'ipk'=>3.72]);
  User::create(['name'=>'Dr. Siti Rahayu','email'=>'dosen@arah.test','password'=>Hash::make('password')]);
  foreach([
   ['IF301','Pemrograman Web','Programming',3],['IF302','UI/UX Design','UI/UX',3],['IF303','Data Mining','Data',3],['IF304','Kecerdasan Buatan','AI',3],['IF305','Jaringan Komputer','Jaringan',3],['IF306','Keamanan Informasi','Keamanan',3],['IF307','Manajemen Proyek TI','Manajemen',3]
  ] as [$kode,$nama,$kategori,$sks]) MataKuliah::create(compact('kode','nama','kategori','sks'));
 }
}
