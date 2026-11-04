@extends('layouts.app')

@section('title', 'Dashboard — ARAH')

@section('content')

@php
$jam  = now('Asia/Jakarta')->hour;
$sapa = ($jam >= 18 || $jam < 4) ? 'Selamat malam' : ($jam < 11 ? 'Selamat pagi' : ($jam < 15 ? 'Selamat siang' : 'Selamat sore'));
$nama = explode(' ', trim($m->nama))[0];

$s2   = $minat->count() > 0;
$s3   = $topRecommendations->count() > 0;
$done = 1 + (int) $s2 + (int) $s3;
$pct  = round($done / 3 * 100);
$cta  = $s2 ? ['Lihat rekomendasi', 'recommendations'] : ['Isi asesmen minat', 'assessment.show'];
$langkah = [
    ['Buat akun ARAH',       'Akunmu sudah aktif',              true],
    ['Isi asesmen minat',    'Ceritakan minat dan kemampuanmu', $s2],
    ['Dapatkan rekomendasi', 'Mata kuliah beserta alasannya',   $s3],
];

$ic = [
    'grad'  => '<path d="M12 3l9 5-9 5-9-5 9-5z"/><path d="M7 11v5c0 1.5 2.2 3 5 3s5-1.5 5-3v-5"/>',
    'bars'  => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
    'cal'   => '<rect x="3" y="5" width="18" height="16" rx="3"/><path d="M3 10h18M8 3v4M16 3v4"/>',
    'spark' => '<path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z"/>',
    'chat'  => '<path d="M21 12a8 8 0 0 1-11.5 7.2L4 20l1-4.3A8 8 0 1 1 21 12z"/>',
    'ok'    => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
    'pie'   => '<path d="M21 12a9 9 0 1 1-9-9v9z"/>',
];

$ipk  = (float) $m->ipk;
$rata = $rataNilai !== null ? (float) $rataNilai : 0;
$sem  = (int) $m->semester;
$snap = [
    ['IPK',             'grad', $ipk > 0 ? number_format($ipk, 2) : null,  'Indeks Prestasi Kumulatif', $ipk > 0 ? min(100, $ipk / 4 * 100) : null],
    ['Rata-rata nilai', 'bars', $rata > 0 ? number_format($rata, 1) : null, 'Berdasarkan riwayat nilai', $rata > 0 ? min(100, $rata) : null],
    ['Semester',        'cal',  $sem > 0 ? $sem : null,                     'Semester aktif',            null],
];

$minatUrut = $s2 ? $minat->sortByDesc('skor')->values() : collect();
$now = false;

@endphp

<style>
    @property --p { syntax: '<number>'; inherits: false; initial-value: 0; }

    .arah-page { --ink:#1E2A6B; --pri:#4F46E5; --vio:#7C3AED; --tx:#334155; --mut:#64748b; --line:#e5e3ff; --soft:#f8f7ff;
        position: relative; overflow: hidden; min-height: calc(100vh - 64px); padding: 28px 22px 56px; color: var(--tx);
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
        background: linear-gradient(165deg, #1b1760 0%, #3a2ea6 50%, #6b35e0 100%); }
    .arah-page *, .arah-page *::before, .arah-page *::after { box-sizing: border-box; }
    :where(.arah-page) h1, :where(.arah-page) h2, :where(.arah-page) p { margin: 0; }
    .a-wrap { position: relative; z-index: 2; width: min(1200px, 100%); margin: 0 auto; }
    .i { width: 20px; height: 20px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

    /* Latar ungu */
    .a-bg { position: absolute; inset: 0; overflow: hidden; pointer-events: none; z-index: 0; }
    .a-orb { position: absolute; border-radius: 50%; filter: blur(80px); animation: dr 26s ease-in-out infinite alternate; }
    .a-o1 { width: 500px; height: 500px; top: -160px; left: -140px; background: #8b5cf6; opacity: .55; }
    .a-o2 { width: 420px; height: 420px; bottom: -140px; right: -120px; background: #c026d3; opacity: .28; animation-duration: 30s; }
    @keyframes dr { to { transform: translate(110px, 80px) scale(1.12); } }
    .a-gridbg { position: absolute; inset: 0; background-size: 56px 56px; animation: gm 40s linear infinite;
        background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
        -webkit-mask-image: linear-gradient(#000 20%, transparent 95%); mask-image: linear-gradient(#000 20%, transparent 95%); }
    @keyframes gm { to { background-position: 0 56px, 56px 0; } }
    .a-arw { position: absolute; bottom: -40px; left: var(--x); width: var(--s); height: var(--s); opacity: 0; animation: up var(--d) linear infinite; animation-delay: var(--dl); }
    .a-arw::before { content: ""; position: absolute; inset: 0; border-radius: 3px; border-top: 2.5px solid rgba(255,255,255,.6); border-left: 2.5px solid rgba(255,255,255,.6); transform: rotate(45deg) scale(.7); }
    @keyframes up { 0% { transform: translateY(0); opacity: 0; } 12%, 88% { opacity: .4; } 100% { transform: translateY(-115vh); opacity: 0; } }

    /* Panel kaca */
    .a-gl { position: relative; overflow: hidden; color: #fff; border: 1px solid rgba(255,255,255,.26); border-radius: 28px; backdrop-filter: blur(18px);
        background: linear-gradient(135deg, rgba(255,255,255,.17), rgba(255,255,255,.06)); box-shadow: 0 30px 70px rgba(10,6,60,.35), inset 0 1px 0 rgba(255,255,255,.35); }
    .a-hero { position: relative; display: grid; grid-template-columns: 1fr auto; align-items: center; gap: 24px; margin-bottom: 22px; padding: 6px 4px 0; color: #fff; }
    .a-hero h1 { margin-bottom: 8px; font-size: clamp(28px, 4.4vw, 44px); line-height: 1.15; font-weight: 700; letter-spacing: -.03em; overflow-wrap: anywhere; }
    .a-hero p { max-width: 520px; color: rgba(255,255,255,.82); font-size: 14px; line-height: 1.75; }
    .a-hm { position: relative; width: 250px; height: 200px; display: grid; place-items: center; }
    .a-hm img { width: 230px; height: 230px; object-fit: contain; filter: drop-shadow(0 18px 28px rgba(10,6,60,.45)) drop-shadow(0 0 28px rgba(196,181,253,.35)); animation: bob 4s ease-in-out infinite; }
    @keyframes bob { 0%, 100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-8px) rotate(2deg); } }
    @keyframes spin { to { transform: rotate(360deg); } }

    .a-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 44px; margin-top: 20px; padding: 0 20px; border-radius: 13px; font-size: 12.5px; font-weight: 600; text-decoration: none; transition: transform .2s, background .2s; }
    .a-btn:hover { transform: translateY(-2px); }
    .a-btn:focus-visible, .a-lnk:focus-visible { outline: 3px solid rgba(255,255,255,.7); outline-offset: 3px; }
    .a-btn.w { color: var(--ink); background: #fff; box-shadow: 0 10px 24px rgba(10,6,60,.3); }
    .a-btn.g { margin-top: 0; color: #fff; background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.34); }
    .a-btn.g:hover { background: rgba(255,255,255,.25); }

    /* Perjalanan (hilang jika onboarding selesai) */
    .a-jny { margin-bottom: 32px; padding: 22px 0 0; color: #fff; border-top: 1px solid rgba(255,255,255,.2); }
    .a-jh { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .a-jh h2 { font-size: 15px; font-weight: 700; }
    .a-jh span { padding: 5px 12px; border-radius: 99px; background: rgba(255,255,255,.16); font-size: 11.5px; font-weight: 600; }
    .a-jr { position: relative; display: grid; grid-template-columns: repeat(3, 1fr); margin-top: 20px; }
    .a-jl { position: absolute; top: 19px; left: 16.667%; right: 16.667%; height: 3px; overflow: hidden; border-radius: 9px; background: rgba(255,255,255,.2); }
    .a-jl i { display: block; height: 100%; width: var(--w); background: linear-gradient(90deg, #c4b5fd, #fff); animation: grow 1.2s cubic-bezier(.2,.8,.2,1) .4s backwards; }
    .a-js { position: relative; padding: 0 10px; text-align: center; }
    .a-jc { position: relative; z-index: 1; width: 40px; height: 40px; margin: 0 auto 10px; display: grid; place-items: center; border-radius: 50%; border: 2px solid rgba(255,255,255,.35); background: #4a3fc4; font-size: 13px; font-weight: 700; }
    .a-js.done .a-jc { color: var(--pri); background: #fff; border-color: #fff; }
    .a-js.now .a-jc { border-color: #fff; animation: rp 2s infinite; }
    @keyframes rp { 0% { box-shadow: 0 0 0 0 rgba(255,255,255,.55); } 70%, 100% { box-shadow: 0 0 0 12px rgba(255,255,255,0); } }
    .a-jt { font-size: 13px; font-weight: 600; }
    .a-jd { margin-top: 3px; color: rgba(255,255,255,.7); font-size: 11.5px; line-height: 1.5; }

    /* Kartu putih */
    .a-card { background: #fff; border: 1px solid rgba(255,255,255,.7); border-radius: 22px; box-shadow: 0 18px 46px rgba(10,6,60,.28); transition: transform .25s, box-shadow .25s; }
    .a-card:hover { transform: translateY(-3px); box-shadow: 0 26px 56px rgba(10,6,60,.36); }
    .a-panel { padding: 24px; }
    .a-h2 { color: var(--ink); font-size: 17px; font-weight: 700; letter-spacing: -.01em; }
    .a-sub { margin: 4px 0 16px; color: var(--mut); font-size: 12px; line-height: 1.6; }
    .l.a-h2 { color: #fff; }
    .l.a-sub { color: rgba(255,255,255,.72); }
    .a-fi { animation: fi .5s ease calc(var(--i, 0) * 70ms) backwards; }
    @keyframes fi { from { opacity: 0; transform: translateY(14px); } }
    @keyframes grow { from { width: 0; } }

    /* Statistik */
    .a-grid3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-bottom: 26px; }
    .a-sn { padding: 20px; }
    .a-ico { width: 44px; height: 44px; display: grid; place-items: center; border-radius: 14px; color: var(--pri); background: linear-gradient(135deg, #eef2ff, #f3e8ff); }
    .a-lb { margin-top: 14px; color: var(--mut); font-size: 12px; font-weight: 500; }
    .a-val { margin-top: 2px; color: var(--ink); font-size: 30px; line-height: 1.2; font-weight: 700; letter-spacing: -.03em; }
    .a-val.na { color: #94a3b8; font-size: 15px; font-weight: 500; letter-spacing: 0; line-height: 2.4; }
    .a-nt { margin-top: 2px; color: #94a3b8; font-size: 11px; }
    .a-mini { height: 5px; margin-top: 12px; overflow: hidden; border-radius: 99px; background: #eceaff; }
    .a-mini i, .a-fill { display: block; height: 100%; width: var(--w); border-radius: inherit; background: linear-gradient(90deg, var(--pri), var(--vio)); animation: grow 1.2s cubic-bezier(.2,.8,.2,1) .3s backwards; }

    /* Minat + NARA */
    .a-2c { display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(300px, .6fr); gap: 18px; margin-bottom: 26px; }
    .a-ir { display: grid; grid-template-columns: 170px 1fr 46px; align-items: center; gap: 14px; }
    .a-ir + .a-ir { margin-top: 18px; }
    .a-nm { font-size: 12.5px; font-weight: 600; }
    .a-nm em { margin-left: 6px; padding: 2px 8px; border-radius: 99px; font-style: normal; font-size: 10px; color: #6d28d9; background: #f3e8ff; }
    .a-tr { height: 10px; overflow: hidden; border-radius: 99px; background: #eceaff; }
    .a-pc { text-align: right; color: var(--pri); font-size: 12.5px; font-weight: 700; }
    .a-nara { background: linear-gradient(145deg, #f5f3ff, #fff 75%); }
    .a-live { display: inline-block; width: 8px; height: 8px; margin-right: 8px; border-radius: 50%; background: #10b981; animation: lv 2s infinite; }
    @keyframes lv { 0% { box-shadow: 0 0 0 0 rgba(16,185,129,.5); } 70%, 100% { box-shadow: 0 0 0 8px rgba(16,185,129,0); } }
    .a-qb { display: inline-flex; align-items: center; gap: 7px; flex-shrink: 0; min-height: 36px; padding: 0 14px; border-radius: 11px; color: var(--pri); background: #eef2ff; border: 1px solid #e0e7ff; font-size: 12px; font-weight: 600; text-decoration: none; transition: transform .2s, background .2s; }
    .a-qb .i { width: 16px; height: 16px; }
    .a-qb:hover { background: #e3e7ff; transform: translateY(-2px); }
    .a-qb:focus-visible { outline: 2px solid var(--pri); outline-offset: 2px; }
    .a-lnk { display: inline-flex; margin-top: 14px; color: var(--pri); font-size: 12px; font-weight: 600; text-decoration: none; }
    .a-lnk:hover { text-decoration: underline; text-underline-offset: 3px; }
    .a-empty { padding: 26px; text-align: center; color: var(--mut); background: var(--soft); border: 1px dashed #cfcaff; border-radius: 16px; font-size: 12px; line-height: 1.8; }
    .a-empty.dk { color: rgba(255,255,255,.85); background: rgba(255,255,255,.08); border-color: rgba(255,255,255,.35); }
    .a-rad { width: 84px; height: 84px; margin-bottom: 6px; animation: spin 40s linear infinite; }

    /* Rekomendasi */
    .a-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 15px; margin-bottom: 16px; }
    .a-head .a-sub { margin-bottom: 0; }
    .a-grid3r { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
    .a-rec { padding: 20px; }
    .a-rec.top { box-shadow: 0 0 0 2px #c4b5fd, 0 22px 50px rgba(10,6,60,.35); background: linear-gradient(180deg, #faf8ff, #fff 60%); }
    .a-rk { display: flex; align-items: center; justify-content: space-between; }
    .a-no { padding: 5px 11px; border-radius: 99px; color: var(--pri); background: #eef2ff; font-size: 11px; font-weight: 700; }
    .a-rec.top .a-no { color: #fff; background: linear-gradient(135deg, var(--pri), var(--vio)); }
    .a-ring { --p: var(--v); position: relative; width: 54px; height: 54px; display: grid; place-items: center; border-radius: 50%; background: conic-gradient(var(--pri) calc(var(--p) * 1%), #eceaff 0); animation: ringin 1.3s cubic-bezier(.2,.8,.2,1) .3s backwards; }
    @keyframes ringin { from { --p: 0; } }
    .a-ring::before { content: ""; position: absolute; inset: 6px; border-radius: 50%; background: #fff; }
    .a-ring b { position: relative; color: var(--ink); font-size: 12px; }
    .a-cc { margin-top: 14px; color: #94a3b8; font-size: 10.5px; font-weight: 600; }
    .a-cn { margin-top: 3px; color: var(--ink); font-size: 15.5px; line-height: 1.4; font-weight: 700; }
    .a-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
    .a-tag { padding: 4px 9px; border-radius: 8px; color: #5b5bd6; background: var(--soft); border: 1px solid var(--line); font-size: 10.5px; font-weight: 600; }
    .a-rs { margin-top: 14px; padding-top: 12px; border-top: 1px dashed var(--line); color: var(--mut); font-size: 11.5px; line-height: 1.75; }

    @media (max-width: 1000px) { .a-2c { grid-template-columns: 1fr; } .a-grid3r { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 760px) {
        .arah-page { padding: 16px 12px 36px; }
        .a-hero { grid-template-columns: 1fr; padding: 4px 0 0; }
        .a-hero > div:first-child { padding-right: 100px; }
        .a-hm { position: absolute; top: 0; right: 0; width: 104px; height: 104px; }
        .a-hm img { width: 96px; height: 96px; }
        .a-btn { width: 100%; }
        .a-jny { padding: 18px 0 0; }
        .a-jr { grid-template-columns: 1fr; gap: 16px; }
        .a-jl { display: none; }
        .a-js { display: flex; align-items: flex-start; gap: 12px; padding: 0; text-align: left; }
        .a-jc { margin: 0; flex-shrink: 0; }
        .a-grid3 { grid-template-columns: 1fr; }
        .a-grid3r { grid-template-columns: 1fr; }
        .a-ir { grid-template-columns: 100px 1fr 40px; gap: 8px; }
        .a-nm em { display: none; }
        .a-head { flex-direction: column; align-items: flex-start; }
    }
    @media (prefers-reduced-motion: reduce) { .arah-page *, .arah-page *::before, .arah-page *::after { animation: none !important; transition: none !important; } }
</style>

<div class="arah-page">

```
<div class="a-bg" aria-hidden="true">
    <div class="a-gridbg"></div>
    <i class="a-orb a-o1"></i><i class="a-orb a-o2"></i>
    <i class="a-arw" style="--x:8%;  --s:22px; --d:18s; --dl:-3s"></i>
    <i class="a-arw" style="--x:58%; --s:26px; --d:20s; --dl:-8s"></i>
    <i class="a-arw" style="--x:90%; --s:20px; --d:22s; --dl:-13s"></i>
</div>
```

<div class="a-wrap">

```
{{-- HERO: satu tombol utama --}}
<section class="a-hero a-fi">
    <div>
        <h1>{{ $sapa }}, {{ $nama }}</h1>
        <p>Pantau kondisi akademikmu, pahami profil minat, dan lihat rekomendasi studi yang sudah dianalisis oleh ARAH dan NARA.</p>
        <a href="{{ route($cta[1]) }}" class="a-btn w"><svg class="i" viewBox="0 0 24 24" style="width:16px;height:16px">{!! $ic['spark'] !!}</svg>{{ $cta[0] }}</a>
    </div>
    <div class="a-hm"><img src="{{ asset('images/happy.png') }}" alt="NARA"></div>
</section>

{{-- PERJALANAN: hilang kalau asesmen dan rekomendasi sudah selesai --}}
@if (! ($s2 && $s3))
    <section class="a-jny a-fi" style="--i:1">
        <div class="a-jh"><h2>Perjalanan ARAH</h2><span>{{ $done }} dari 3 langkah, {{ $pct }}%</span></div>
        <div class="a-jr">
            <div class="a-jl"><i style="--w:{{ max(0, $done - 1) * 50 }}%"></i></div>
            @foreach ($langkah as $k => $st)
                @php $isNow = ! $st[2] && ! $now; if ($isNow) { $now = true; } @endphp
                <div class="a-js {{ $st[2] ? 'done' : '' }} {{ $isNow ? 'now' : '' }}">
                    <div class="a-jc">@if ($st[2])<svg class="i" viewBox="0 0 24 24" style="width:18px;height:18px">{!! $ic['ok'] !!}</svg>@else{{ $k + 1 }}@endif</div>
                    <div>
                        <div class="a-jt">{{ $st[0] }}</div>
                        <div class="a-jd">{{ $st[1] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif

{{-- STATISTIK --}}
<section>
    <h2 class="a-h2 l">Ringkasan akademik</h2>
    <p class="a-sub l">Kondisi akademikmu saat ini.</p>
    <div class="a-grid3">
        @foreach ($snap as $i => $s)
            <div class="a-card a-sn a-fi" style="--i:{{ $i + 2 }}">
                <span class="a-ico"><svg class="i" viewBox="0 0 24 24">{!! $ic[$s[1]] !!}</svg></span>
                <div class="a-lb">{{ $s[0] }}</div>
                @if ($s[2] !== null)
                    <div class="a-val">{{ $s[2] }}</div>
                @else
                    <div class="a-val na">Belum tersedia</div>
                @endif
                <div class="a-nt">{{ $s[3] }}</div>
                @if ($s[4] !== null)<div class="a-mini"><i style="--w:{{ round($s[4]) }}%"></i></div>@endif
            </div>
        @endforeach
    </div>
</section>

{{-- DOSEN PA --}}
<section class="a-card a-panel a-fi" style="--i:5; margin-bottom:26px;">
    <div style="
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:20px;
        flex-wrap:wrap;
    ">

        <div style="min-width:0;">
            <div style="
                color:#94a3b8;
                font-size:10.5px;
                font-weight:600;
                text-transform:uppercase;
                letter-spacing:.04em;
                margin-bottom:6px;
            ">
                Pembimbing Akademik
            </div>

            <h2 class="a-h2">
                Dosen Pembimbing Akademik
            </h2>

            <p class="a-sub" style="margin-bottom:0;">
                Dosen PA yang ditugaskan untuk mendampingi proses akademikmu.
            </p>
        </div>

        <div style="
            flex-shrink:0;
            width:48px;
            height:48px;
            display:grid;
            place-items:center;
            border-radius:14px;
            color:var(--pri);
            background:linear-gradient(135deg,#eef2ff,#f3e8ff);
        ">
            <svg class="i" viewBox="0 0 24 24">
                <circle cx="12" cy="8" r="3.5"/>
                <path d="M5 20c.8-3.2 3.1-5 7-5s6.2 1.8 7 5"/>
            </svg>
        </div>

    </div>

    @if ($m->dosenPa)

        <div style="
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
            margin-top:20px;
            padding:18px;
            border:1px solid var(--line);
            border-radius:16px;
            background:linear-gradient(135deg,#faf9ff,#fff);
        ">

            <div style="min-width:0;">

                <div style="
                    color:var(--ink);
                    font-size:16px;
                    font-weight:700;
                    overflow-wrap:anywhere;
                ">
                    {{ $m->dosenPa->nama }}
                </div>

                @if ($m->dosenPa->nip)
                    <div style="
                        margin-top:5px;
                        color:var(--mut);
                        font-size:11.5px;
                    ">
                        NIP {{ $m->dosenPa->nip }}
                    </div>
                @endif

                @if ($m->dosenPa->kampus)
                    <div style="
                        margin-top:3px;
                        color:var(--mut);
                        font-size:11.5px;
                    ">
                        {{ $m->dosenPa->kampus->nama }}
                    </div>
                @endif

            </div>

            <a
    href="{{ route('chat-dosen') }}"
    class="a-qb"
    style="margin-top:14px;"
>
    <svg class="i" viewBox="0 0 24 24">
        <path d="M21 12a8 8 0 0 1-11.5 7.2L4 20l1-4.3A8 8 0 1 1 21 12z"/>
    </svg>
    Chat Dosen
</a>

            <div style="
                flex-shrink:0;
                padding:6px 10px;
                border-radius:999px;
                color:#166534;
                background:#dcfce7;
                font-size:10.5px;
                font-weight:600;
            ">
                Dosen PA
            </div>

        </div>

    @else

        <div style="
            margin-top:20px;
            padding:16px 18px;
            border:1px dashed #cfcaff;
            border-radius:16px;
            color:#92400e;
            background:#fffaf0;
            font-size:12px;
            line-height:1.7;
        ">
            Dosen Pembimbing Akademik belum ditentukan oleh administrator kampus.
        </div>

    @endif
</section>

{{-- MINAT + NARA --}}
<div class="a-2c">
    <section class="a-card a-panel a-fi" style="--i:5">
        <div class="a-head" style="align-items:flex-start">
            <div>
                <h2 class="a-h2">Profil minat</h2>
                <p class="a-sub">Distribusi minat dari asesmen terakhir.</p>
            </div>
            <a href="{{ route('interest.profile') }}" class="a-qb"><svg class="i" viewBox="0 0 24 24">{!! $ic['pie'] !!}</svg>Lihat profil minat</a>
        </div>
        @if ($s2)
            @foreach ($minatUrut as $item)
                <div class="a-ir">
                    <div class="a-nm">{{ $item->kategori }}@if ($loop->first)<em>Tertinggi</em>@endif</div>
                    <div class="a-tr"><div class="a-fill" style="--w:{{ min(100, max(0, (float) $item->skor)) }}%"></div></div>
                    <div class="a-pc">{{ round($item->skor) }}%</div>
                </div>
            @endforeach
        @else
            <div class="a-empty">
                <svg class="a-rad" viewBox="0 0 120 120" fill="none" stroke="#c4b5fd" stroke-width="1.5"><polygon points="60,10 105,35 105,85 60,110 15,85 15,35"/><polygon points="60,30 87,45 87,75 60,90 33,75 33,45" stroke="#a78bfa"/><circle cx="60" cy="60" r="4" fill="#7C3AED" stroke="none"/></svg><br>
                Profil minatmu belum terbentuk. Isi asesmen supaya NARA bisa memetakan minat dan kemampuanmu.
            </div>
        @endif
    </section>

    <section class="a-card a-panel a-nara a-fi" style="--i:6">
        <h2 class="a-h2" style="margin-bottom:14px"><span class="a-live"></span>Insight dari NARA</h2>
        <x-nara-bubble :message="$naraInsight" />
        <a href="{{ route('chat') }}" class="a-lnk">Konsultasikan dengan NARA →</a>
    </section>
</div>

{{-- REKOMENDASI --}}
<section>
    <div class="a-head">
        <div>
            <h2 class="a-h2 l">Rekomendasi utama</h2>
            <p class="a-sub l">Mata kuliah dengan skor kecocokan tertinggi dari analisis ARAH.</p>
        </div>
        @if ($s3)<a href="{{ route('recommendations') }}" class="a-btn g">Lihat semua →</a>@endif
    </div>

    @if ($s3)
        <div class="a-grid3r">
            @foreach ($topRecommendations as $index => $item)
                @php $mk = $item->mataKuliah; @endphp
                <div class="a-card a-rec a-fi {{ $index === 0 ? 'top' : '' }}" style="--i:{{ $index + 7 }}">
                    <div class="a-rk">
                        <span class="a-no">{{ $index === 0 ? 'Paling cocok' : '#'.($index + 1) }}</span>
                        <div class="a-ring" style="--v:{{ min(100, max(0, (float) $item->skor)) }}"><b>{{ round($item->skor) }}%</b></div>
                    </div>
                    <div class="a-cc">{{ $mk?->kode ?? '—' }}</div>
                    <div class="a-cn">{{ $mk?->nama ?? 'Mata kuliah' }}</div>
                    <div class="a-tags">
                        @if ($mk?->kategori)<span class="a-tag">{{ $mk->kategori }}</span>@endif
                        @if ($mk?->sks)<span class="a-tag">{{ $mk->sks }} SKS</span>@endif
                    </div>
                    @if ($item->alasan)<div class="a-rs">{{ $item->alasan }}</div>@endif
                </div>
            @endforeach
        </div>
    @else
        <div class="a-empty dk">Belum ada rekomendasi. Rekomendasi muncul setelah asesmen selesai.</div>
    @endif
</section>
```

</div>
</div>

@endsection
