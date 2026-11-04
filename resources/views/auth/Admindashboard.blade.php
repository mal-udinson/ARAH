@extends('layouts.app')

@section('title', 'Dashboard Admin Kampus — ARAH')

@section('content')

@php
    $snap = [
        ['Mahasiswa', $stats['mahasiswa'], 'Terdaftar di '.$kampus->nama],
        ['Dosen', $stats['dosen'], 'Dosen pembimbing aktif'],
        ['Asesmen selesai', $stats['pct_asesmen'].'%', 'Dari seluruh mahasiswa'],
        ['Rata-rata IPK', $stats['rata_ipk'] ? number_format($stats['rata_ipk'], 2) : null, 'Seluruh mahasiswa'],
    ];
@endphp

@include('partials.arah-style')

<style>
    .a-grid4 { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-bottom: 26px; }
    .d-in { width: 100%; min-height: 40px; padding: 0 14px; border: 1px solid var(--line); border-radius: 11px; font: inherit; font-size: 12.5px; color: var(--tx); background: #fff; }
    textarea.d-in { padding: 10px 14px; min-height: 80px; resize: vertical; }
    .d-in:focus-visible { outline: 2px solid var(--pri); outline-offset: 1px; }
    .d-lb { display: block; margin: 12px 0 5px; color: var(--mut); font-size: 11.5px; font-weight: 600; }
    .d-b { min-height: 38px; margin-top: 14px; padding: 0 16px; border: 0; border-radius: 10px; font: inherit; font-size: 12px; font-weight: 600; color: #fff; background: var(--pri); cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; }
    .d-b.o { color: var(--pri); background: #eef2ff; }
    .d-b:focus-visible { outline: 2px solid var(--pri); outline-offset: 2px; }
    .d-sc { overflow-x: auto; }
    .d-tb { width: 100%; border-collapse: collapse; font-size: 12.5px; min-width: 520px; }
    .d-tb th { padding: 9px 10px; text-align: left; color: var(--mut); font-size: 11px; font-weight: 600; border-bottom: 1px solid var(--line); }
    .d-tb td { padding: 11px 10px; border-bottom: 1px solid #f1efff; }
    .d-chip { display: inline-block; padding: 3px 9px; border-radius: 99px; font-size: 10.5px; font-weight: 600; color: #b45309; background: #fef3c7; }
    .d-chip.ok { color: #047857; background: #d1fae5; }
    .d-ok { margin-bottom: 14px; padding: 10px 14px; border-radius: 11px; color: #047857; background: #d1fae5; font-size: 12px; }
    .d-qa { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; }
    .d-qa a { padding: 14px; border-radius: 14px; color: var(--ink); background: var(--soft); border: 1px solid var(--line); font-size: 12.5px; font-weight: 600; text-decoration: none; transition: transform .2s; }
    .d-qa a:hover { transform: translateY(-2px); }
    .d-qa small { display: block; margin-top: 3px; color: var(--mut); font-weight: 400; font-size: 11px; }
    .d-st { margin-bottom: 26px; }
    @media (max-width: 1000px) { .a-grid4, .d-qa { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 760px) { .a-grid4, .d-qa { grid-template-columns: 1fr; } }
</style>

<div class="arah-page">
    <div class="a-bg" aria-hidden="true"><div class="a-gridbg"></div><i class="a-orb a-o1"></i><i class="a-orb a-o2"></i></div>

<div class="a-wrap">

    <section class="a-hero a-fi">
        <div>
            <h1>Admin {{ $kampus->nama }}</h1>
            <p>Kelola pengguna, penugasan dosen pembimbing, dan pengaturan ARAH untuk kampusmu.</p>
            <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:20px">
                <a href="#import" class="a-btn w" style="margin-top:0">Import data</a>
                <a href="{{ route('admin.export', 'xlsx') }}" class="a-btn g">Export laporan</a>
            </div>
        </div>
        <div class="a-hm"><img src="{{ asset('images/happy.png') }}" alt="NARA"></div>
    </section>

    @if (session('ok'))<div class="d-ok" role="status">{{ session('ok') }}</div>@endif
    @if ($errors->any())<div class="d-ok" style="color:#b91c1c;background:#fee2e2" role="alert">{{ $errors->first() }}</div>@endif

    {{-- 1. RINGKASAN --}}
    <section>
        <h2 class="a-h2 l">Ringkasan kampus</h2>
        <p class="a-sub l">Kondisi penggunaan ARAH di kampusmu.</p>
        <div class="a-grid4">
            @foreach ($snap as $i => $s)
                <div class="a-card a-sn a-fi" style="--i:{{ $i + 1 }}">
                    <div class="a-lb" style="margin-top:0">{{ $s[0] }}</div>
                    @if ($s[1] !== null)<div class="a-val">{{ $s[1] }}</div>@else<div class="a-val na">Belum tersedia</div>@endif
                    <div class="a-nt">{{ $s[2] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 2 + 4. KELOLA & MASTER DATA --}}
    <section class="a-card a-panel a-fi d-st" style="--i:5">
        <h2 class="a-h2">Kelola data</h2>
        <p class="a-sub">Pengguna dan master data akademik.</p>
        <div class="d-qa">
            <a href="{{ route('admin.mahasiswa.index') }}">Mahasiswa<small>{{ $stats['mahasiswa'] }} akun</small></a>
            <a href="{{ route('admin.dosen.index') }}">Dosen<small>{{ $stats['dosen'] }} akun</small></a>
            <a href="{{ route('admin.prodi.index') }}">Fakultas &amp; prodi<small>Master data</small></a>
            <a href="{{ route('admin.matakuliah.index') }}">Mata kuliah<small>SKS, kategori minat</small></a>
        </div>
    </section>

    {{-- 3 + 5. PENUGASAN & BEBAN DOSEN --}}
    <div class="a-2c">
        <section class="a-card a-panel a-fi" style="--i:6">
            <h2 class="a-h2">Beban dosen pembimbing</h2>
            <p class="a-sub">Jumlah mahasiswa dan aktivitas terakhir per dosen.</p>
            <div class="d-sc">
            <table class="d-tb">
                <thead><tr><th>Dosen</th><th>Mahasiswa</th><th>Aktivitas terakhir</th></tr></thead>
                <tbody>
                @forelse ($bebanDosen as $d)
                    <tr>
                        <td>{{ $d->nama }}</td>
                        <td>{{ $d->mahasiswa_count }}</td>
                        <td>@if ($d->aktif_terakhir)<span class="d-chip ok">{{ $d->aktif_terakhir->diffForHumans() }}</span>@else<span class="d-chip">Belum pernah</span>@endif</td>
                    </tr>
                @empty
                    <tr><td colspan="3"><div class="a-empty">Belum ada dosen.</div></td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
        </section>

        <section class="a-card a-panel a-fi" style="--i:7">
            <h2 class="a-h2">Penugasan massal</h2>
            <p class="a-sub">{{ $tanpaDosen }} mahasiswa belum punya dosen PA.</p>
            <form method="POST" action="{{ route('admin.assign') }}">
                @csrf
                <label class="d-lb" for="as-prodi">Prodi</label>
                <select id="as-prodi" name="prodi_id" class="d-in" required>
                    @foreach ($prodi as $p)<option value="{{ $p->id }}">{{ $p->nama }}</option>@endforeach
                </select>
                <label class="d-lb" for="as-ang">Angkatan</label>
                <input id="as-ang" name="angkatan" type="number" class="d-in" placeholder="Contoh: 2023" required>
                <label class="d-lb" for="as-dosen">Dosen PA</label>
                <select id="as-dosen" name="dosen_id" class="d-in" required>
                    @foreach ($bebanDosen as $d)<option value="{{ $d->id }}">{{ $d->nama }}</option>@endforeach
                </select>
                <button class="d-b">Tugaskan</button>
            </form>
        </section>
    </div>

    {{-- IMPORT --}}
    <section id="import" class="a-card a-panel a-fi d-st" style="--i:8">
        <h2 class="a-h2">Import massal</h2>
        <p class="a-sub">Unggah file CSV atau Excel. <a class="a-lnk" style="margin:0" href="{{ route('admin.import.template', 'mahasiswa') }}">Unduh template mahasiswa</a> · <a class="a-lnk" style="margin:0" href="{{ route('admin.import.template', 'dosen') }}">template dosen</a></p>
        <form method="POST" action="{{ route('admin.import') }}" enctype="multipart/form-data">
            @csrf
            <label class="d-lb" for="im-t">Jenis data</label>
            <select id="im-t" name="tipe" class="d-in" required><option value="mahasiswa">Mahasiswa</option><option value="dosen">Dosen</option></select>
            <label class="d-lb" for="im-f">File</label>
            <input id="im-f" name="file" type="file" accept=".csv,.xlsx" class="d-in" style="padding-top:8px" required>
            <button class="d-b">Import</button>
        </form>
    </section>

    {{-- 8. MINAT PER PRODI --}}
    <section class="a-card a-panel a-fi d-st" style="--i:9">
        <h2 class="a-h2">Minat per prodi</h2>
        <p class="a-sub">Kategori minat teratas dari hasil asesmen.</p>
        @forelse ($minatPerProdi as $row)
            <div class="a-ir" style="grid-template-columns:170px 1fr 46px">
                <div class="a-nm">{{ $row->prodi }} <em>{{ $row->kategori }}</em></div>
                <div class="a-tr"><div class="a-fill" style="--w:{{ min(100, max(0, (float) $row->skor)) }}%"></div></div>
                <div class="a-pc">{{ round($row->skor) }}%</div>
            </div>
        @empty
            <div class="a-empty">Belum ada data asesmen.</div>
        @endforelse
    </section>

    {{-- 7 + 10. PENGATURAN & PENGUMUMAN --}}
    <div class="a-2c" style="grid-template-columns:repeat(2,minmax(0,1fr))">
        <section class="a-card a-panel a-fi" style="--i:10">
            <h2 class="a-h2">Pengaturan kampus</h2>
            <p class="a-sub">Ambang mahasiswa "perlu perhatian" di dashboard dosen.</p>
            <form method="POST" action="{{ route('admin.settings') }}">
                @csrf @method('PUT')
                <label class="d-lb" for="s-ipk">Batas IPK rendah</label>
                <input id="s-ipk" name="ipk_min" type="number" step="0.01" min="0" max="4" class="d-in" value="{{ $kampus->ipk_min ?? 2.75 }}">
                <label class="d-lb" for="s-sem">Semester akhir mulai dari</label>
                <input id="s-sem" name="semester_akhir" type="number" min="1" max="14" class="d-in" value="{{ $kampus->semester_akhir ?? 7 }}">
                <button class="d-b">Simpan</button>
            </form>
        </section>

        <section class="a-card a-panel a-fi" style="--i:11">
            <h2 class="a-h2">Pengumuman</h2>
            <p class="a-sub">Kirim ke dosen atau mahasiswa.</p>
            <form method="POST" action="{{ route('admin.announce') }}">
                @csrf
                <label class="d-lb" for="p-to">Tujuan</label>
                <select id="p-to" name="tujuan" class="d-in"><option value="semua">Semua</option><option value="dosen">Dosen</option><option value="mahasiswa">Mahasiswa</option></select>
                <label class="d-lb" for="p-msg">Pesan</label>
                <textarea id="p-msg" name="pesan" class="d-in" maxlength="500" required></textarea>
                <button class="d-b">Kirim</button>
            </form>
        </section>
    </div>

    {{-- 9. AUDIT LOG --}}
    <section class="a-card a-panel a-fi" style="--i:12; margin-top:26px">
        <div class="a-head" style="align-items:flex-start">
            <div><h2 class="a-h2">Audit log</h2><p class="a-sub">Siapa melihat atau mengubah data sensitif.</p></div>
            <a href="{{ route('admin.audit') }}" class="d-b o" style="margin-top:0">Lihat semua</a>
        </div>
        <div class="d-sc">
        <table class="d-tb">
            <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse ($auditLog as $log)
                <tr><td>{{ $log->created_at->format('d M H:i') }}</td><td>{{ $log->user->nama ?? '—' }}</td><td>{{ $log->aksi }}</td></tr>
            @empty
                <tr><td colspan="3"><div class="a-empty">Belum ada aktivitas.</div></td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </section>

</div>
</div>

@endsection