<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{AuditLog, Dosen, Mahasiswa, Minat, Pesan, Prodi};
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    // Route admin dibungkus middleware: auth + role:admin_kampus
    // Semua query di bawah otomatis terfilter kampus lewat BelongsToKampus.

    public function index()
    {
        $kampus = auth()->user()->kampus;

        $total = Mahasiswa::count();
        $sudah = Mahasiswa::has('minat')->count();
        $ipk   = Mahasiswa::where('ipk', '>', 0)->avg('ipk');

        $stats = [
            'mahasiswa'   => $total,
            'dosen'       => Dosen::count(),
            'pct_asesmen' => $total ? round($sudah / $total * 100) : 0,
            'rata_ipk'    => $ipk ? (float) $ipk : null,
        ];

        $bebanDosen = Dosen::withCount('mahasiswa')
            ->withMax(['pesanKeluar as aktif_terakhir' => fn ($q) => $q], 'created_at')
            ->orderByDesc('mahasiswa_count')->get()
            ->each(fn ($d) => $d->aktif_terakhir = $d->aktif_terakhir ? \Carbon\Carbon::parse($d->aktif_terakhir) : null);

        $minatPerProdi = Minat::join('mahasiswa', 'mahasiswa.id', '=', 'minat.mahasiswa_id')
            ->join('prodi', 'prodi.id', '=', 'mahasiswa.prodi_id')
            ->selectRaw('prodi.nama as prodi, minat.kategori, AVG(minat.skor) as skor')
            ->groupBy('prodi.nama', 'minat.kategori')->orderByDesc('skor')->limit(8)->get();

        return view('admin.dashboard', [
            'kampus'        => $kampus,
            'stats'         => $stats,
            'bebanDosen'    => $bebanDosen,
            'tanpaDosen'    => Mahasiswa::whereNull('dosen_id')->count(),
            'prodi'         => Prodi::orderBy('nama')->get(),
            'minatPerProdi' => $minatPerProdi,
            'auditLog'      => AuditLog::with('user')->latest()->limit(8)->get(),
        ]);
    }

    public function assign(Request $r)
    {
        $d = $r->validate(['prodi_id' => 'required|integer', 'angkatan' => 'required|integer', 'dosen_id' => 'required|integer']);

        // exists() lewat model ikut terscope kampus, jadi tidak bisa menunjuk dosen kampus lain
        abort_unless(Dosen::whereKey($d['dosen_id'])->exists(), 403);

        $n = Mahasiswa::where('prodi_id', $d['prodi_id'])->where('angkatan', $d['angkatan'])->update(['dosen_id' => $d['dosen_id']]);

        AuditLog::create(['user_id' => auth()->id(), 'aksi' => "Penugasan massal: $n mahasiswa"]);
        return back()->with('ok', "$n mahasiswa ditugaskan.");
    }

    public function settings(Request $r)
    {
        auth()->user()->kampus->update($r->validate([
            'ipk_min' => 'required|numeric|between:0,4', 'semester_akhir' => 'required|integer|between:1,14',
        ]));
        return back()->with('ok', 'Pengaturan disimpan.');
    }

    // import(), announce(), export(), audit(): pakai maatwebsite/excel dan tabel pengumuman.
    // Di import, jangan percaya kampus_id dari file; trait mengisinya dari admin yang login.
}