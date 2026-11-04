@extends('layouts.app')

@section('title', 'Dashboard Dosen - ARAH')

@section('content')

@php
$dosen        = $dosen ?? auth()->user()?->dosen ?? (object) ['nama' => auth()->user()?->name, 'kampus' => null];
$mahasiswa    = $mahasiswa ?? collect();
$perhatian    = $perhatian ?? collect();
$pengajuan    = $pengajuan ?? collect();
$minatAgregat = $minatAgregat ?? collect();
$stats        = array_merge(['total' => 0, 'sudah_asesmen' => 0, 'pct_asesmen' => 0, 'rata_ipk' => null], $stats ?? []);
    $jam  = now('Asia/Jakarta')->hour;
    $sapa = $jam < 11 ? 'Selamat pagi' : ($jam < 15 ? 'Selamat siang' : ($jam < 18 ? 'Selamat sore' : 'Selamat malam'));
    $kampus = $dosen->kampus?->nama;
    $unread = (int) $mahasiswa->sum('unread_count');
    $nPerhatian = $perhatian->count();
    $rata = $stats['rata_ipk'];

    $st = fn ($v) => $v <= 0 ? ['Belum ada data', 'n'] : ($v >= 3.5 ? ['Sangat baik', 'g'] : ($v >= 3 ? ['Baik', 'b'] : ($v >= 2.75 ? ['Cukup', 'a'] : ['IPK rendah', 'r'])));
    $tone = ['IPK rendah' => 'r', 'Belum asesmen' => 'a', 'Semester akhir' => 'b'];

    $ic = [
        'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.5a3.5 3.5 0 0 1 0 7M18 14a6.5 6.5 0 0 1 3.5 6"/>',
        'bars'  => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
        'spark' => '<path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z"/>',
        'build' => '<path d="M4 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16M16 9h2a2 2 0 0 1 2 2v10M2 21h20M8 7h4M8 11h4M8 15h4"/>',
        'find'  => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/>',
        'go'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'warn'  => '<path d="M12 3l10 18H2L12 3zM12 10v5M12 18h.01"/>',
        'ok'    => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
        'mail'  => '<rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 8l9 6 9-6"/>',
        'cal'   => '<rect x="3" y="5" width="18" height="16" rx="3"/><path d="M3 10h18M8 3v4M16 3v4"/>',
    ];

    $cards = [
        ['Mahasiswa bimbingan', 'users', $stats['total'], 0, '', 'Di bawah bimbinganmu', null, ''],
        ['Perlu perhatian', 'warn', $nPerhatian, 0, '', 'IPK, asesmen, semester', null, $nPerhatian ? 'warn' : ''],
        ['Sudah asesmen', 'spark', $stats['pct_asesmen'], 0, '%', $stats['sudah_asesmen'].' dari '.$stats['total'].' mahasiswa', $stats['pct_asesmen'], ''],
        ['Rata-rata IPK', 'bars', $rata, 2, '', $rata === null ? 'Belum ada data IPK' : 'Skala 4,00', $rata === null ? null : min(100, $rata / 4 * 100), ''],
    ];
@endphp

<style>
    .ds { --ink:#1E2A6B; --pri:#4F46E5; --sky:#0ea5e9; --tx:#334155; --mut:#64748b; --line:#e0e7ff; --soft:#f5f7ff;
        position: relative; overflow: hidden; min-height: calc(100vh - 64px); padding: 30px 22px 60px; color: var(--tx);
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; background: linear-gradient(165deg, #14235f 0%, #3a3db5 52%, #0b8ad6 100%); }
    .ds *, .ds *::before, .ds *::after { box-sizing: border-box; }
    .ds-wrap { position: relative; z-index: 2; width: min(1280px, 100%); margin: 0 auto; }
    .i { width: 20px; height: 20px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }

    .ds-bg { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
    .ds-orb { position: absolute; border-radius: 50%; filter: blur(80px); }
    .ds-o1 { width: 500px; height: 500px; top: -160px; left: -140px; background: #6366f1; opacity: .55; animation: dr 22s ease-in-out infinite alternate; }
    .ds-o2 { width: 440px; height: 440px; bottom: -150px; right: -130px; background: #38bdf8; opacity: .42; animation: dr 26s ease-in-out infinite alternate-reverse; }
    @keyframes dr { to { transform: translate(120px, 90px) scale(1.15); } }
    .ds-grid { position: absolute; inset: 0; background-size: 56px 56px; animation: gm 40s linear infinite;
        background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
        -webkit-mask-image: linear-gradient(#000 20%, transparent 95%); mask-image: linear-gradient(#000 20%, transparent 95%); }
    @keyframes gm { to { background-position: 0 56px, 56px 0; } }
    .ds-arw { position: absolute; bottom: -40px; left: var(--x); width: var(--s); height: var(--s); opacity: 0; animation: up var(--d) linear infinite; animation-delay: var(--dl); }
    .ds-arw::before { content: ""; position: absolute; inset: 0; border-radius: 3px; border-top: 2.5px solid rgba(255,255,255,.6); border-left: 2.5px solid rgba(255,255,255,.6); transform: rotate(45deg) scale(.7); }
    @keyframes up { 0% { transform: translateY(0); opacity: 0; } 12%, 88% { opacity: .45; } 100% { transform: translateY(-115vh); opacity: 0; } }

    /* Hero transparan: teks langsung di atas gradasi */
    .ds-hero { display: grid; grid-template-columns: 1fr auto; align-items: center; gap: 24px; margin-bottom: 26px; padding: 14px 8px 8px; color: #fff; }
    .ds-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .ds-chip { display: inline-flex; align-items: center; gap: 7px; padding: 6px 12px; border-radius: 99px; background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.26); font-size: 11.5px; font-weight: 600; }
    .ds-chip .i { width: 14px; height: 14px; }
    .ds-chip.warn { background: rgba(251,191,36,.22); border-color: rgba(251,191,36,.55); }
    .ds-hero h1 { margin: 0 0 12px; font-size: clamp(28px, 4.4vw, 46px); line-height: 1.12; font-weight: 700; letter-spacing: -.035em; }
    .ds-hero p { margin: 0; max-width: 560px; color: rgba(255,255,255,.85); font-size: 14.5px; line-height: 1.75; }
    .ds-hm { position: relative; width: 250px; height: 220px; display: grid; place-items: center; }
    .ds-hm::before { content: ""; position: absolute; inset: 10px 20px; border-radius: 50%; background: radial-gradient(circle, rgba(255,255,255,.34), transparent 68%); animation: pu 3.2s ease-in-out infinite; }
    .ds-hm img { position: relative; z-index: 1; width: 200px; height: 200px; object-fit: contain; filter: drop-shadow(0 18px 28px rgba(8,16,70,.45)); animation: bob 4s ease-in-out infinite; }
    @keyframes bob { 0%, 100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-10px) rotate(2deg); } }
    @keyframes pu { 50% { transform: scale(1.12); opacity: .6; } }

    .ds-card { position: relative; overflow: hidden; background: #fff; border: 1px solid rgba(255,255,255,.7); border-radius: 22px; box-shadow: 0 18px 46px rgba(8,16,70,.28); transition: transform .25s, box-shadow .25s; }
    .ds-card::before { content: ""; position: absolute; inset: 0; pointer-events: none; opacity: 0; transition: opacity .25s; background: radial-gradient(260px circle at var(--mx, 50%) var(--my, 50%), rgba(14,165,233,.14), transparent 70%); }
    .ds-card:hover::before { opacity: 1; }
    .ds-stat:hover { transform: translateY(-4px); box-shadow: 0 26px 56px rgba(8,16,70,.36); }
    .js .rv:not(.in) { opacity: 0; }
    .js .rv.in { animation: rvin .6s ease var(--d, 0ms) backwards; }
    @keyframes rvin { from { opacity: 0; transform: translateY(18px); } }
    .js .rv:not(.in) .ds-mini i, .js .rv:not(.in) .ds-fill { width: 0; }

    .ds-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 22px; }
    .ds-stat { padding: 20px; }
    .ds-ico { width: 44px; height: 44px; display: grid; place-items: center; border-radius: 14px; color: var(--pri); background: linear-gradient(135deg, #eef2ff, #e0f2fe); transition: transform .3s; }
    .ds-stat.warn .ds-ico { color: #b45309; background: #fef3c7; }
    .ds-stat:hover .ds-ico { transform: rotate(-8deg) scale(1.08); }
    .ds-lb { margin-top: 14px; color: var(--mut); font-size: 12px; font-weight: 500; }
    .ds-val { margin-top: 2px; color: var(--ink); font-size: 30px; line-height: 1.2; font-weight: 700; letter-spacing: -.03em; }
    .ds-nt { margin-top: 2px; color: #94a3b8; font-size: 11px; }
    .ds-mini { height: 5px; margin-top: 12px; overflow: hidden; border-radius: 99px; background: #e8edff; }
    .ds-mini i { display: block; height: 100%; width: var(--w); border-radius: inherit; background: linear-gradient(90deg, var(--pri), var(--sky)); transition: width 1.2s cubic-bezier(.2,.8,.2,1) .3s; }

        .ds-2c { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; margin-bottom: 22px; align-items: stretch; }
    .ds-stack { display: contents; }
    .ds-pn { display: flex; flex-direction: column; padding: 22px 24px; }
    .ds-pn .ds-empty, .ds-pn .ds-ok { margin: auto 0; }
    @media (max-width: 1180px) {
        .ds-2c { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .ds-2c > :first-child { grid-column: 1 / -1; }
    }
    .ds-h { margin: 0; color: var(--ink); font-size: 17px; font-weight: 700; letter-spacing: -.01em; }
    .ds-h em { margin-left: 8px; padding: 3px 10px; border-radius: 99px; background: #eef2ff; color: var(--pri); font-style: normal; font-size: 11px; font-weight: 600; vertical-align: 2px; }
    .ds-sub { margin: 4px 0 16px; color: var(--mut); font-size: 12.5px; }

    .ds-row { display: flex; align-items: center; gap: 12px; padding: 12px 0; border-top: 1px solid #eef1fb; }
    .ds-row:first-of-type { border-top: 0; }
    .ds-av { width: 38px; height: 38px; flex-shrink: 0; display: grid; place-items: center; border-radius: 12px; color: #fff; font-size: 14px; font-weight: 700; background: linear-gradient(135deg, var(--pri), var(--sky)); box-shadow: 0 6px 14px rgba(79,70,229,.25); }
    .ds-info { flex: 1; min-width: 0; }
    .ds-nm { color: var(--ink); font-size: 13px; font-weight: 600; }
    .ds-em { margin-top: 2px; color: #94a3b8; font-size: 11px; }
    .ds-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; }
    .ds-bd { padding: 3px 9px; border-radius: 99px; font-size: 10.5px; font-weight: 600; white-space: nowrap; }
    .ds-bd.g { background: #d1fae5; color: #047857; }
    .ds-bd.b { background: #e0e7ff; color: #4338ca; }
    .ds-bd.a { background: #fef3c7; color: #b45309; }
    .ds-bd.r { background: #ffe4e6; color: #be123c; }
    .ds-bd.n { background: #f1f5f9; color: #64748b; }
    .ds-un { margin-left: 8px; padding: 2px 8px; border-radius: 99px; background: #0ea5e9; color: #fff; font-size: 10px; font-weight: 600; white-space: nowrap; }

    .ds-btn { position: relative; overflow: hidden; display: inline-flex; align-items: center; gap: 7px; padding: 9px 14px; border-radius: 11px; color: #fff; background: linear-gradient(135deg, #1E2A6B, #4F46E5); box-shadow: 0 8px 18px rgba(30,42,107,.28); font-size: 11.5px; font-weight: 600; text-decoration: none; white-space: nowrap; transition: transform .2s, box-shadow .2s; }
    .ds-btn.sm { padding: 7px 11px; }
    .ds-btn .i { width: 14px; height: 14px; transition: transform .2s; }
    .ds-btn::after { content: ""; position: absolute; top: 0; left: -60%; width: 40%; height: 100%; background: linear-gradient(100deg, transparent, rgba(255,255,255,.5), transparent); transform: skewX(-20deg); transition: left .6s; }
    .ds-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(30,42,107,.38); }
    .ds-btn:hover::after { left: 130%; }
    .ds-btn:hover .i { transform: translateX(3px); }
    .ds-btn:focus-visible { outline: 3px solid #a5b4fc; outline-offset: 2px; }

    .ds-ir { display: grid; grid-template-columns: 110px 1fr 40px; align-items: center; gap: 10px; }
    .ds-ir + .ds-ir { margin-top: 14px; }
    .ds-ir span:first-child { color: var(--tx); font-size: 12px; font-weight: 600; }
    .ds-ir b { text-align: right; color: var(--pri); font-size: 12px; }
    .ds-tr { height: 8px; overflow: hidden; border-radius: 99px; background: #e8edff; }
    .ds-fill { height: 100%; width: var(--w); border-radius: inherit; background: linear-gradient(90deg, var(--pri), var(--sky)); transition: width 1.2s cubic-bezier(.2,.8,.2,1) .2s; }
    .ds-ok { display: flex; align-items: center; gap: 10px; padding: 16px; border-radius: 14px; background: #ecfdf5; color: #047857; font-size: 12.5px; }
    .ds-empty { padding: 28px 14px; text-align: center; color: #94a3b8; font-size: 12.5px; line-height: 1.8; }

    .ds-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; padding: 22px 24px; border-bottom: 1px solid var(--line); }
    .ds-find { position: relative; width: min(300px, 100%); }
    .ds-find .i { position: absolute; left: 13px; top: 12px; width: 16px; height: 16px; color: #a3a3c2; }
    .ds-find input { width: 100%; height: 40px; padding: 0 14px 0 38px; border: 1px solid var(--line); border-radius: 12px; background: var(--soft); color: var(--tx); font: 12.5px 'Poppins', sans-serif; outline: 0; transition: border-color .2s, box-shadow .2s, background .2s; }
    .ds-find input:focus { background: #fff; border-color: #818cf8; box-shadow: 0 0 0 4px rgba(79,70,229,.12); }
    .ds-scroll { overflow-x: auto; }
    .ds table { width: 100%; min-width: 860px; border-collapse: collapse; }
    .ds th { padding: 13px 24px; background: var(--soft); color: var(--mut); font-size: 11px; font-weight: 600; text-align: left; }
    .ds td { padding: 14px 24px; border-top: 1px solid #eef1fb; font-size: 13px; vertical-align: middle; }
    .ds tbody tr { transition: background .2s; }
    .ds tbody tr:hover { background: #f8faff; }
    .ds .r { text-align: right; }
    .ds-ipk { display: flex; align-items: center; gap: 10px; }
    .ds-ipk b { min-width: 34px; color: var(--ink); font-size: 14px; }
    #noRes { display: none; }

    @media (max-width: 1000px) { .ds-stats { grid-template-columns: repeat(2, 1fr); } .ds-2c { grid-template-columns: 1fr; } }
    @media (max-width: 760px) {
        .ds { padding: 16px 12px 36px; }
        .ds-hero { grid-template-columns: 1fr; padding: 6px 0 0; }
        .ds-hm { position: absolute; top: 4px; right: 0; width: 96px; height: 96px; }
        .ds-hm img { width: 78px; height: 78px; }
        .ds-hero > div:first-child { padding-right: 84px; }
        .ds-head, .ds-pn { padding: 18px; }
        .ds-find { width: 100%; }
    }
    @media (max-width: 460px) { .ds-stats { gap: 9px; } .ds-stat { padding: 14px; } .ds-val { font-size: 24px; } }
    @media (prefers-reduced-motion: reduce) { .ds *, .ds *::before, .ds *::after { animation: none !important; transition: none !important; } }
</style>

<div class="ds">

    <div class="ds-bg" aria-hidden="true">
        <div class="ds-grid"></div><i class="ds-orb ds-o1"></i><i class="ds-orb ds-o2"></i>
        <i class="ds-arw" style="--x:8%;  --s:22px; --d:18s; --dl:-3s"></i>
        <i class="ds-arw" style="--x:66%; --s:26px; --d:20s; --dl:-6s"></i>
        <i class="ds-arw" style="--x:92%; --s:20px; --d:24s; --dl:-14s"></i>
    </div>

<div class="ds-wrap">

    {{-- HERO --}}
    <section class="ds-hero">
        <div>
            <div class="ds-chips">
                @if ($kampus)<span class="ds-chip"><svg class="i" viewBox="0 0 24 24">{!! $ic['build'] !!}</svg>{{ $kampus }}</span>@endif
                @if ($stats['total'] === 0)
                    <span class="ds-chip">Belum ada mahasiswa bimbingan</span>
                @elseif ($nPerhatian > 0)
                    <span class="ds-chip warn"><svg class="i" viewBox="0 0 24 24">{!! $ic['warn'] !!}</svg>{{ $nPerhatian }} mahasiswa perlu perhatian</span>
                @else
                    <span class="ds-chip"><svg class="i" viewBox="0 0 24 24">{!! $ic['ok'] !!}</svg>Semua mahasiswa dalam kondisi baik</span>
                @endif
                @if ($pengajuan->count())<span class="ds-chip"><svg class="i" viewBox="0 0 24 24">{!! $ic['cal'] !!}</svg>{{ $pengajuan->count() }} pengajuan bimbingan</span>@endif
                @if ($unread)<span class="ds-chip"><svg class="i" viewBox="0 0 24 24">{!! $ic['mail'] !!}</svg>{{ $unread }} pesan baru</span>@endif
            </div>
            <h1>{{ $sapa }}, {{ $dosen->nama }}</h1>
            <p>Pantau kondisi akademik mahasiswa bimbinganmu, dan siapkan bahan diskusi perwalian bersama ARAH dan NARA.</p>
        </div>
        <div class="ds-hm"><img src="{{ asset('images/happy.png') }}" alt="NARA"></div>
    </section>

    {{-- STATISTIK --}}
    <div class="ds-stats">
        @foreach ($cards as $c)
            <div class="ds-card ds-stat fx rv {{ $c[7] }}">
                <span class="ds-ico"><svg class="i" viewBox="0 0 24 24">{!! $ic[$c[1]] !!}</svg></span>
                <div class="ds-lb">{{ $c[0] }}</div>
                @if ($c[2] === null)
                    <div class="ds-val">—</div>
                @else
                    <div class="ds-val" data-count="{{ $c[2] }}" data-dec="{{ $c[3] }}" data-suf="{{ $c[4] }}">{{ number_format($c[2], $c[3]) }}{{ $c[4] }}</div>
                @endif
                <div class="ds-nt">{{ $c[5] }}</div>
                @if ($c[6] !== null)<div class="ds-mini"><i style="--w:{{ round($c[6]) }}%"></i></div>@endif
            </div>
        @endforeach
    </div>

    <div class="ds-2c">

        {{-- PERLU PERHATIAN --}}
        <section class="ds-card ds-pn fx rv">
            <h2 class="ds-h">Perlu perhatian<em>{{ $nPerhatian }}</em></h2>
            <p class="ds-sub">Mahasiswa yang sebaiknya didampingi lebih dulu.</p>
            @forelse ($perhatian->take(4) as $m)
                <div class="ds-row">
                    <span class="ds-av">{{ strtoupper(mb_substr($m->nama, 0, 1)) }}</span>
                    <div class="ds-info">
                        <div class="ds-nm">{{ $m->nama }}<span style="color:#94a3b8;font-weight:400"> · {{ $m->nim }}</span></div>
                        <div class="ds-tags">
                            @foreach ($m->alasan_perhatian as $a)<span class="ds-bd {{ $tone[$a] ?? 'n' }}">{{ $a }}</span>@endforeach
                        </div>
                    </div>
                    <a href="{{ route('dosen.mahasiswa.show', $m->id) }}" class="ds-btn sm">Lihat<svg class="i" viewBox="0 0 24 24">{!! $ic['go'] !!}</svg></a>
                </div>
            @empty
                <div class="ds-ok"><svg class="i" viewBox="0 0 24 24">{!! $ic['ok'] !!}</svg>Tidak ada mahasiswa yang perlu perhatian khusus saat ini.</div>
            @endforelse
            @if ($nPerhatian > 4)<div class="ds-sub" style="margin:12px 0 0">dan {{ $nPerhatian - 6 }} mahasiswa lainnya, cek di tabel di bawah.</div>@endif
        </section>

        <div class="ds-stack">
            {{-- PENGAJUAN --}}
            <section class="ds-card ds-pn fx rv">
                <h2 class="ds-h">Pengajuan bimbingan<em>{{ $pengajuan->count() }}</em></h2>
                <p class="ds-sub">Jadwal yang menunggu persetujuanmu.</p>
                @forelse ($pengajuan->take(4) as $p)
                    <div class="ds-row">
                        <span class="ds-av">{{ strtoupper(mb_substr($p->mahasiswa->nama ?? 'M', 0, 1)) }}</span>
                        <div class="ds-info">
                            <div class="ds-nm">{{ $p->mahasiswa->nama ?? 'Mahasiswa' }}</div>
                            <div class="ds-em">{{ $p->waktu ? \Carbon\Carbon::parse($p->waktu)->locale('id')->translatedFormat('D, d M Y · H:i') : 'Waktu belum ditentukan' }}</div>
                        </div>
                        @if ($p->mahasiswa)<a href="{{ route('dosen.mahasiswa.show', $p->mahasiswa->id) }}" class="ds-btn sm">Buka</a>@endif
                    </div>
                @empty
                    <div class="ds-empty">Tidak ada pengajuan yang menunggu.</div>
                @endforelse
            </section>

            {{-- MINAT --}}
            <section class="ds-card ds-pn fx rv">
                <h2 class="ds-h">Minat mahasiswamu</h2>
                <p class="ds-sub">Rata-rata skor minat dari asesmen yang sudah diisi.</p>
                @forelse ($minatAgregat->take(5) as $mn)
                    <div class="ds-ir">
                        <span>{{ $mn->kategori }}</span>
                        <div class="ds-tr"><div class="ds-fill" style="--w:{{ min(100, max(0, round($mn->skor))) }}%"></div></div>
                        <b>{{ round($mn->skor) }}%</b>
                    </div>
                @empty
                    <div class="ds-empty">Belum ada mahasiswa yang mengisi asesmen.</div>
                @endforelse
            </section>
        </div>
    </div>

    {{-- TABEL MAHASISWA --}}
    <section class="ds-card rv">
        <div class="ds-head">
            <div>
                <h2 class="ds-h">Mahasiswa bimbingan<em id="cnt">{{ $mahasiswa->count() }}</em></h2>
                <p class="ds-sub" style="margin-bottom:0">Daftar lengkap mahasiswa di bawah bimbinganmu.</p>
            </div>
            @if ($mahasiswa->count() > 0)
                <div class="ds-find">
                    <svg class="i" viewBox="0 0 24 24">{!! $ic['find'] !!}</svg>
                    <input type="search" id="q" placeholder="Cari nama atau NIM" aria-label="Cari mahasiswa">
                </div>
            @endif
        </div>

        @if ($mahasiswa->count() > 0)
            <div class="ds-scroll">
                <table>
                    <thead><tr><th>NIM</th><th>Nama</th><th>Semester</th><th>IPK</th><th>Asesmen</th><th class="r">Aksi</th></tr></thead>
                    <tbody id="rows">
                        @foreach ($mahasiswa as $item)
                            @php $ipk = (float) $item->ipk; $s = $st($ipk); @endphp
                            <tr>
                                <td>{{ $item->nim }}</td>
                                <td>
                                    <div class="ds-nm">{{ $item->nama }}@if ($item->unread_count > 0)<span class="ds-un">{{ $item->unread_count }} pesan baru</span>@endif</div>
                                    @if ($item->user)<div class="ds-em">{{ $item->user->email }}</div>@endif
                                </td>
                                <td>Semester {{ $item->semester }}</td>
                                <td><div class="ds-ipk"><b>{{ $ipk > 0 ? number_format($ipk, 2) : '—' }}</b><span class="ds-bd {{ $s[1] }}">{{ $s[0] }}</span></div></td>
                                <td>@if ($item->minat_count > 0)<span class="ds-bd g">Sudah</span>@else<span class="ds-bd a">Belum</span>@endif</td>
                                <td class="r"><a href="{{ route('dosen.mahasiswa.show', $item->id) }}" class="ds-btn">Lihat detail<svg class="i" viewBox="0 0 24 24">{!! $ic['go'] !!}</svg></a></td>
                            </tr>
                        @endforeach
                        <tr id="noRes"><td colspan="6"><div class="ds-empty">Tidak ada mahasiswa yang cocok dengan pencarianmu.</div></td></tr>
                    </tbody>
                </table>
            </div>
        @else
            <div class="ds-empty">Belum ada mahasiswa bimbingan.<br>Mahasiswa yang memilihmu sebagai Dosen PA akan muncul di sini.</div>
        @endif
    </section>

</div>
</div>

<script>
(function () {
    var pg = document.querySelector('.ds');
    pg.classList.add('js');
    var rm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function count(el) {
        var end = parseFloat(el.dataset.count), dec = +el.dataset.dec || 0, suf = el.dataset.suf || '', t0;
        if (rm || isNaN(end)) return;
        function f(t) {
            t0 = t0 || t;
            var p = Math.min((t - t0) / 1100, 1);
            el.textContent = (end * (1 - Math.pow(1 - p, 3))).toFixed(dec) + suf;
            if (p < 1) requestAnimationFrame(f);
        }
        requestAnimationFrame(f);
    }

    var io = new IntersectionObserver(function (es) {
        es.forEach(function (e) {
            if (!e.isIntersecting) return;
            e.target.classList.add('in');
            e.target.querySelectorAll('[data-count]').forEach(count);
            io.unobserve(e.target);
        });
    }, { threshold: .15 });
    pg.querySelectorAll('.rv').forEach(function (el, i) { el.style.setProperty('--d', (i % 4) * 80 + 'ms'); io.observe(el); });

    pg.querySelectorAll('.fx').forEach(function (c) {
        c.addEventListener('pointermove', function (e) {
            var r = c.getBoundingClientRect();
            c.style.setProperty('--mx', e.clientX - r.left + 'px');
            c.style.setProperty('--my', e.clientY - r.top + 'px');
        });
    });

    var q = document.getElementById('q');
    if (q) {
        var rows = [].slice.call(document.querySelectorAll('#rows tr:not(#noRes)')), none = document.getElementById('noRes'), cnt = document.getElementById('cnt');
        q.addEventListener('input', function () {
            var t = q.value.trim().toLowerCase(), n = 0;
            rows.forEach(function (r) {
                var ok = r.textContent.toLowerCase().indexOf(t) > -1;
                r.style.display = ok ? '' : 'none';
                if (ok) n++;
            });
            none.style.display = n ? 'none' : 'table-row';
            cnt.textContent = n;
        });
    }
})();
</script>

@endsection