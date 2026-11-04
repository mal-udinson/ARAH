<?php

namespace Database\Seeders;

use App\Models\MataKuliah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mengisi tabel mata_kuliah dari daftar KRS (semester 1-8).
 *
 * - Aman dijalankan berulang: baris yang kodenya sudah ada TIDAK diubah.
 *   Satu-satunya perubahan pada baris lama: kolom semester diisi jika masih kosong.
 * - Kolom semester hanya dipakai kalau memang ada di tabel.
 *
 * Jalankan:  php artisan db:seed --class=MataKuliahKrsSeeder
 */
class MataKuliahKrsSeeder extends Seeder
{
    /**
     * Kolom `kategori` wajib diisi di tabel mata_kuliah, sedangkan KRS tidak
     * memuat kategori. Ganti nilai ini kalau aplikasi memakai kategori tertentu.
     */
    private const KATEGORI_DEFAULT = 'Umum';

    public function run(): void
    {
        $adaSemester = Schema::hasColumn('mata_kuliah', 'semester');
        $baru = 0;
        $sudahAda = 0;
        $semesterDiisi = 0;

        DB::transaction(function () use ($adaSemester, &$baru, &$sudahAda, &$semesterDiisi) {
            foreach ($this->data() as [$semester, $kode, $nama, $sks]) {
                $isi = [
                    'nama'     => $nama,
                    'kategori' => self::KATEGORI_DEFAULT,
                    'sks'      => $sks,
                ];

                if ($adaSemester) {
                    $isi['semester'] = $semester;
                }

                $mk = MataKuliah::firstOrCreate(['kode' => $kode], $isi);

                if ($mk->wasRecentlyCreated) {
                    $baru++;
                    continue;
                }

                $sudahAda++;

                if ($adaSemester && $mk->semester === null) {
                    $mk->update(['semester' => $semester]);
                    $semesterDiisi++;
                }
            }
        });

        $this->command?->info("Mata kuliah baru: {$baru} | sudah ada (dilewati): {$sudahAda} | semester diisi: {$semesterDiisi}");
    }

    /**
     * [semester, kode, nama, sks]
     * Nama di KRS terpotong 30 karakter, jadi singkatan dilengkapi.
     */
    private function data(): array
    {
        return [
            // Semester 1
            [1, '03045001', 'Pendidikan Agama Islam', 2],
            [1, '03045002', 'Pendidikan Pancasila', 2],
            [1, '03045011', 'Kalkulus', 2],
            [1, '03045012', 'Matematika Diskrit', 3],
            [1, '03045014', 'Bahasa Inggris', 2],
            [1, '03045018', 'Algoritma dan Dasar Pemrograman', 2],
            [1, '03045019', 'Praktikum Algoritma dan Dasar Pemrograman', 1],
            [1, '03045020', 'Prinsip Sistem dan Teknologi Informasi', 2],
            [1, '03045021', 'Pengantar Teknologi Digital dan Elektronika', 2],
            [1, '03045040', 'Pengantar Manajemen dan Bisnis', 2],
            [1, '030450400', 'Manajemen Informasi Perusahaan', 2],
            [1, '03045101', 'Kalkulus', 3],
            [1, '03045102', 'Dasar Sistem Digital dan Teknologi Informasi', 3],
            [1, '03045108', 'Pendidikan Agama', 2],

            // Semester 2
            [2, '03045003', 'Akidah', 2],
            [2, '03045004', 'Sosio Informasi dan Profesionalisme', 2],
            [2, '03045005', 'Pendidikan Kewarganegaraan', 2],
            [2, '03045013', 'Aljabar Linear', 2],
            [2, '03045022', 'Bahasa Indonesia', 2],
            [2, '03045023', 'Organisasi dan Arsitektur Sistem Komputer', 3],
            [2, '03045024', 'Struktur Data', 2],
            [2, '03045025', 'Praktikum Struktur Data', 1],
            [2, '03045041', 'E-Business', 2],
            [2, '03045042', 'Metodologi Pengembangan Sistem Informasi', 2],
            [2, '03045103', 'Aljabar Linear', 3],
            [2, '03045105', 'Aqidah', 2],

            // Semester 3
            [3, '03015109', 'Ibadah-Akhlaq', 2],
            [3, '030450026', 'Jaringan Komputer dan Komunikasi Data', 3],
            [3, '030450044', 'Teknologi Antarmuka Komputer Lingkungan', 2],
            [3, '03045006', 'Ibadah Akhlak', 2],
            [3, '03045015', 'Statistika dan Probabilitas', 3],
            [3, '03045027', 'Pemrograman Berorientasi Objek', 2],
            [3, '03045028', 'Praktikum Pemrograman Berorientasi Objek', 1],
            [3, '03045029', 'Sistem Basis Data', 2],
            [3, '03045030', 'Praktikum Sistem Basis Data', 1],
            [3, '03045031', 'Sistem Operasi', 3],
            [3, '03045043', 'Perencanaan Solusi Sistem Informasi', 2],
            [3, '03045044', 'Antarmuka Komputer Lingkungan', 2],
            [3, '03045081', 'Ketrampilan Komunikasi (Kompetensi)', 2],

            // Semester 4
            [4, '03045007', 'Muamalah', 2],
            [4, '03045032', 'Pemrograman Web', 2],
            [4, '03045033', 'Praktikum Pemrograman Web', 1],
            [4, '03045034', 'Keamanan Informasi dan Jaringan', 3],
            [4, '03045045', 'Sistem Terdistribusi', 3],
            [4, '03045046', 'Sistem Informasi Akuntansi', 2],
            [4, '03045047', 'Analisis dan Perancangan Sistem Informasi', 3],
            [4, '03045048', 'Internet of Things (IoT)', 3],
            [4, '03045049', 'Desain UI/UX', 2],

            // Semester 5
            [5, '03045008', 'Kemuhammadiyahan', 2],
            [5, '03045016', 'Manajemen Proyek dan Perubahan', 3],
            [5, '03045050', 'Pengembangan Sistem Informasi Manajemen', 3],
            [5, '03045051', 'Mobile Application', 2],
            [5, '03045052', 'Praktikum Mobile Application', 1],
            [5, '03045053', 'Perancangan Infrastruktur Teknologi Informasi', 2],
            [5, '03045075', 'Berpikir Kritis dan Kreatif (Kompetensi)', 2],
            [5, '03045076', 'Empati dan Kecerdasan Sosial', 2],
            [5, '03045077', 'Etika Profesional (Kompetensi)', 2],
            [5, '03045078', 'Inovasi dan Pemikiran Desain', 3],
            [5, '03045079', 'Keberagaman dan Multibudaya', 2],
            [5, '03045080', 'Kepemimpinan Inklusif', 3],
            [5, '03045083', 'Kewirausahaan Sosial', 2],
            [5, '03045084', 'Kewirausahaan Terstruktur', 3],
            [5, '03045085', 'Pemecahan Masalah (Kompetensi)', 2],
            [5, '03045086', 'Penelitian', 2],
            [5, '03045088', 'Pengembangan Kompetensi', 3],
            [5, '03045089', 'Pengembangan Profesi', 2],
            [5, '03045090', 'Pengembangan Talenta', 3],
            [5, '03045091', 'Publikasi Nasional', 2],
            [5, '03045092', 'Publikasi Internasional', 3],
            [5, '03045094', 'Strategi Negosiasi', 2],
            [5, '03045095', 'Summer Course', 3],
            [5, '03045096', 'Kewirausahaan Bisnis', 2],
            [5, '03045097', 'Keterampilan Berbahasa Jepang', 2],
            [5, '03045098', 'Pancasila dan Kewarganegaraan', 3],
            [5, '03045104', 'Keterampilan Berbicara Bahasa Jepang', 2],

            // Semester 6
            [6, '03045009', 'Islam dan Disiplin Ilmu (IDI)', 2],
            [6, '03045035', 'Riset Teknologi Informasi', 2],
            [6, '03045036', 'Praktik Kerja', 2],
            [6, '03045054', 'Pengujian dan Integrasi Sistem', 2],
            [6, '03045055', 'Praktikum Pengujian dan Integrasi Sistem', 1],
            [6, '03045060', 'Data Science', 3],
            [6, '03045062', 'Sistem Manajemen Pengetahuan', 3],
            [6, '03045063', 'e-Education', 2],
            [6, '03045064', 'Sistem Informasi Geografis', 2],
            [6, '03045066', 'e-Government', 2],
            [6, '03045099', 'Kecerdasan Buatan', 2],
            [6, '03045107', 'Islam untuk Disiplin Ilmu', 2],
            [6, '030451070', 'Islam untuk Disiplin Ilmu (IDI)', 2],

            // Semester 7
            [7, '0000001', 'Enterprise Resource Planning (ERP)', 2],
            [7, '0000002', 'Financial Technology', 2],
            [7, '0000003', 'Teknologi dan Manajemen Cloud', 2],
            [7, '03045037', 'Kewirausahaan', 3],
            [7, '03045056', 'Audit System dan IT Governance', 3],
            [7, '03045057', 'Seminar Capstone Project', 2],
            [7, '03045058', 'Sistem Pendukung Keputusan dan Eksekutif', 3],
            [7, '03045059', 'Riset Operasi', 3],
            [7, '03045061', 'Manajemen Resiko', 3],
            [7, '03045067', 'Teknologi dan Manajemen Cloud', 3],
            [7, '03045068', 'Financial Technology', 3],
            [7, '03045069', 'Enterprise Resource Planning', 3],
            [7, '03045100', 'Big Data', 3],

            // Semester 8
            [8, '03045038', 'Skripsi', 6],
        ];
    }
}