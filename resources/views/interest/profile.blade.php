@extends('layouts.app')

@section('title', 'Profil minat — ARAH')

@section('content')

@php
    $nama   = explode(' ', trim($mahasiswa->nama))[0];
    $daftar = collect($asesmen)->sortByDesc('skor')->values();
    $ada    = (bool) $tertinggi && $daftar->count() > 0;
    $dna    = min(100, max(0, (float) $dnaIndex));
    $abil   = (float) $academicAbility;

    // Urutan bidang sama dengan $radarScores: 0 Pemrograman, 1 UI/UX, 2 Data, 3 Sistem, 4 Bisnis
    $namaBidang = ['Pemrograman', 'UI/UX', 'Data', 'Sistem', 'Bisnis'];
    $rs         = collect($radarScores)->values();
    $skorBidang = array_map(fn ($i) => max(0, min(100, (float) data_get($rs->get($i), 'skor', 0))), range(0, 4));

    // [nama, deskripsi, ikon, bidang utama (bobot 2), bidang pendukung (bobot 1)]
    $karier = collect([
        ['Business & System Analyst', 'Menjembatani kebutuhan bisnis dengan sistem informasi: menganalisis proses dan data, lalu merumuskan solusi yang bisa dikerjakan tim teknis.', '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>', [2, 4], [3]],
        ['Chief Information Officer (CIO)', 'Pemimpin teknologi informasi di sebuah organisasi. Menentukan arah strategi TI dan memastikan teknologi benar-benar mendukung tujuan bisnis.', '<path d="M12 3l8 4v5c0 4.5-3.2 8-8 9-4.8-1-8-4.5-8-9V7l8-4z"/>', [4], [3, 2]],
        ['Technopreneurship', 'Membangun usaha berbasis teknologi, mulai dari menemukan masalah, merancang produk digital, sampai menjalankan bisnisnya.', '<path d="M9 18h6M10 21h4M12 3a6 6 0 0 0-3.5 10.9c.6.5 1 1.2 1 2.1h5c0-.9.4-1.6 1-2.1A6 6 0 0 0 12 3z"/>', [4], [0, 1]],
        ['IT Support', 'Menjaga sistem dan perangkat tetap berjalan: menangani gangguan, merawat jaringan, dan membantu pengguna menyelesaikan masalah teknis.', '<path d="M4 14v-2a8 8 0 0 1 16 0v2M4 14h3v5H5a1 1 0 0 1-1-1v-4zM20 14h-3v5h2a1 1 0 0 0 1-1v-4z"/>', [3], [0]],
        ['Project Manager', 'Merencanakan, mengoordinasikan, dan mengawal proyek TI agar selesai tepat waktu dan sesuai kebutuhan pengguna.', '<rect x="3" y="4" width="5" height="16" rx="1.5"/><rect x="10" y="4" width="5" height="10" rx="1.5"/><rect x="17" y="4" width="4" height="13" rx="1.5"/>', [4], [0, 3]],
        ['Programmer', 'Menulis, menguji, dan memperbaiki kode untuk membangun aplikasi atau sistem dari kebutuhan yang ada.', '<path d="M8 8l-4 4 4 4M16 8l4 4-4 4M14 5l-4 14"/>', [0], [3, 2]],
    ])->map(function ($k) use ($skorBidang) {
        $tot = 0; $bobot = 0;
        foreach ($k[3] as $i) { $tot += $skorBidang[$i] * 2; $bobot += 2; }
        foreach ($k[4] as $i) { $tot += $skorBidang[$i];     $bobot += 1; }
        $k[] = $bobot ? (int) round($tot / $bobot) : 0;
        return $k;
    });
    if ($ada) { $karier = $karier->sortByDesc(fn ($k) => $k[5])->values(); }
@endphp

@push('karier')
<section class="ip-sec ip-fi" style="--i:{{ $ada ? 4 : 3 }}">
    <h2>Prospek karier</h2>
    <p>{{ $ada ? 'Karier di bidang Sistem dan Teknologi Informasi, diurutkan dari yang paling selaras dengan minatmu.' : 'Karier yang bisa kamu tuju dari bidang Sistem dan Teknologi Informasi.' }}</p>
    <div class="ip-kg">
        @foreach ($karier as $k)
            <article class="ip-kc {{ $ada && $loop->first ? 'top' : '' }}">
                <div class="ip-kh">
                    <span class="ip-ai"><svg viewBox="0 0 24 24" aria-hidden="true">{!! $k[2] !!}</svg></span>
                    @if ($ada)<span class="ip-km">{{ $k[5] }}% selaras</span>@endif
                </div>
                <b>{{ $k[0] }}</b>
                <p>{{ $k[1] }}</p>
                <div class="ip-kt">
                    @foreach ($k[3] as $i)<span class="pri">{{ $namaBidang[$i] }}</span>@endforeach
                    @foreach ($k[4] as $i)<span>{{ $namaBidang[$i] }}</span>@endforeach
                </div>
            </article>
        @endforeach
    </div>
    @if ($ada)<p class="ip-kn">Keselarasan dihitung dari rata-rata skor minatmu pada bidang terkait (bidang utama berbobot dua kali). Ini panduan awal, bukan penilaian pasti.</p>@endif
</section>
@endpush

<style>
    @property --dna { syntax: '<number>'; inherits: false; initial-value: 0; }

    .ip { --ink:#1E2A6B; --pri:#4F46E5; --vio:#7C3AED; --tx:#334155; --mut:#64748b; --line:#e5e3ff;
        position: relative; overflow: hidden; min-height: calc(100vh - 64px); padding: 30px 22px 56px; color: #fff;
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; background: linear-gradient(165deg, #1b1760 0%, #3a2ea6 50%, #6b35e0 100%); }
    .ip *, .ip *::before, .ip *::after { box-sizing: border-box; }
    :where(.ip) h1, :where(.ip) h2, :where(.ip) p { margin: 0; }
    .ip-bg { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
    .ip-o1, .ip-o2 { position: absolute; border-radius: 50%; filter: blur(80px); animation: ip-dr 28s ease-in-out infinite alternate; }
    .ip-o1 { width: 460px; height: 460px; top: -150px; left: -120px; background: #8b5cf6; opacity: .5; }
    .ip-o2 { width: 400px; height: 400px; bottom: -140px; right: -120px; background: #c026d3; opacity: .26; animation-duration: 32s; }
    @keyframes ip-dr { to { transform: translate(100px, 70px) scale(1.1); } }
    .ip-grid-bg { position: absolute; inset: 0; background-size: 56px 56px; -webkit-mask-image: linear-gradient(#000 20%, transparent 95%); mask-image: linear-gradient(#000 20%, transparent 95%);
        background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px); }
    .ip-wrap { position: relative; z-index: 1; width: min(1100px, 100%); margin: 0 auto; }
    .ip-fi { animation: ip-fi .5s ease calc(var(--i, 0) * 80ms) backwards; }
    @keyframes ip-fi { from { opacity: 0; transform: translateY(14px); } }
    @keyframes ip-grow { from { width: 0; } }

    /* Hero transparan */
    .ip-hero { position: relative; display: flex; align-items: center; justify-content: space-between; gap: 24px; margin-bottom: 26px; padding: 6px 4px 0; }
    .ip-hero h1 { font-size: clamp(28px, 4.4vw, 44px); line-height: 1.15; font-weight: 700; letter-spacing: -.03em; overflow-wrap: anywhere; }
    .ip-hero p { max-width: 520px; margin-top: 8px; color: rgba(255,255,255,.82); font-size: 14px; line-height: 1.75; }
    .ip-hm { flex-shrink: 0; width: 230px; height: 170px; display: grid; place-items: center; }
    .ip-hm img { width: 210px; height: 210px; object-fit: contain; filter: drop-shadow(0 18px 28px rgba(10,6,60,.45)) drop-shadow(0 0 28px rgba(196,181,253,.35)); animation: ip-bob 4s ease-in-out infinite; }
    @keyframes ip-bob { 0%, 100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-8px) rotate(2deg); } }

    /* Kartu putih */
    .ip-card { padding: 24px; color: var(--tx); background: #fff; border-radius: 22px; box-shadow: 0 18px 46px rgba(10,6,60,.28); }
    .ip-h { color: var(--ink); font-size: 17px; font-weight: 700; letter-spacing: -.01em; }
    .ip-sub { margin: 4px 0 16px; color: var(--mut); font-size: 12px; line-height: 1.6; }
    .ip-main { display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(0, .85fr); gap: 18px; margin-bottom: 18px; }

    /* Radar */
    .ip-radar { display: block; width: min(470px, 100%); height: auto; margin: 0 auto; overflow: visible; }
    .ip-radar .g { fill: none; stroke: var(--line); }
    .ip-radar .ax { stroke: var(--line); }
    .ip-radar .ar { fill: rgba(124,58,237,.18); stroke: var(--vio); stroke-width: 2.5; stroke-linejoin: round; transform-origin: 250px 195px; animation: ip-pop .9s cubic-bezier(.2,.8,.2,1) .3s backwards; }
    .ip-radar .pt { fill: #fff; stroke: var(--pri); stroke-width: 2.5; animation: ip-fade .5s ease 1s backwards; }
    .ip-radar .lb { fill: var(--ink); font-size: 12px; font-weight: 600; }
    .ip-radar .lv { fill: var(--pri); font-size: 11px; font-weight: 700; }
    @keyframes ip-pop { from { transform: scale(0); opacity: 0; } }
    @keyframes ip-fade { from { opacity: 0; } }

    /* Analisis */
    .ip-top { margin-bottom: 20px; padding: 16px 18px; color: #fff; border-radius: 16px; background: linear-gradient(135deg, var(--ink), var(--pri) 70%, var(--vio)); }
    .ip-top small { color: rgba(255,255,255,.8); font-size: 11px; }
    .ip-top b { display: block; margin-top: 4px; font-size: 22px; line-height: 1.2; letter-spacing: -.02em; }
    .ip-top span { display: block; margin-top: 4px; color: #ddd6fe; font-size: 12px; font-weight: 600; }
    .ip-ir { display: grid; grid-template-columns: minmax(0, 1fr) 40px; align-items: center; gap: 6px 10px; }
    .ip-ir + .ip-ir { margin-top: 14px; }
    .ip-nm { font-size: 12.5px; font-weight: 600; }
    .ip-pc { text-align: right; color: var(--pri); font-size: 12px; font-weight: 700; }
    .ip-tr { grid-column: 1 / -1; height: 8px; overflow: hidden; border-radius: 99px; background: #eceaff; }
    .ip-tr i { display: block; width: var(--w); height: 100%; border-radius: inherit; background: linear-gradient(90deg, var(--pri), var(--vio)); animation: ip-grow 1s cubic-bezier(.2,.8,.2,1) .3s backwards; }

    /* Academic DNA (satu-satunya panel kaca) */
    .ip-dna { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(0, .9fr); gap: 26px 32px; align-items: center; margin-bottom: 18px; padding: 30px; border: 1px solid rgba(255,255,255,.26); border-radius: 26px; backdrop-filter: blur(18px);
        background: linear-gradient(135deg, rgba(255,255,255,.17), rgba(255,255,255,.06)); box-shadow: 0 30px 70px rgba(10,6,60,.35), inset 0 1px 0 rgba(255,255,255,.35); }
    .ip-chip { display: inline-block; padding: 5px 12px; border-radius: 99px; background: rgba(255,255,255,.16); font-size: 11.5px; font-weight: 600; }
    .ip-dna h2 { margin-top: 12px; font-size: clamp(26px, 3.4vw, 34px); line-height: 1.1; font-weight: 700; letter-spacing: -.03em; }
    .ip-dcat { margin-top: 6px; color: #ddd6fe; font-size: 13px; font-weight: 600; }
    .ip-dd { max-width: 560px; margin-top: 12px; color: rgba(255,255,255,.82); font-size: 13px; line-height: 1.8; }
    .ip-traits { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 16px; }
    .ip-traits span { padding: 5px 12px; border: 1px solid rgba(255,255,255,.3); border-radius: 99px; background: rgba(255,255,255,.1); font-size: 11px; font-weight: 600; }
    .ip-dr { display: grid; grid-template-columns: 132px minmax(0, 1fr); align-items: center; gap: 24px; }
    .ip-rw { position: relative; width: 132px; height: 132px; display: grid; place-items: center; }
    .ip-ring { position: absolute; inset: 0; border-radius: 50%; background: conic-gradient(#fff calc(var(--dna) * 1%), rgba(255,255,255,.2) 0);
        -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 12px), #000 calc(100% - 11px)); mask: radial-gradient(farthest-side, transparent calc(100% - 12px), #000 calc(100% - 11px));
        --dna: var(--v); animation: ip-ring 1.3s cubic-bezier(.2,.8,.2,1) .3s backwards; }
    @keyframes ip-ring { from { --dna: 0; } }
    .ip-rn { position: relative; text-align: center; }
    .ip-rn b { display: block; font-size: 30px; line-height: 1; font-weight: 700; }
    .ip-rn small { display: block; margin-top: 4px; color: rgba(255,255,255,.7); font-size: 10.5px; }
    .ip-mt { display: grid; gap: 16px; }
    .ip-mh { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 12px; font-weight: 600; }
    .ip-mh span:last-child { color: #ddd6fe; }
    .ip-mb { height: 7px; overflow: hidden; border-radius: 99px; background: rgba(255,255,255,.2); }
    .ip-mb i { display: block; width: var(--w); height: 100%; border-radius: inherit; background: linear-gradient(90deg, #c4b5fd, #fff); animation: ip-grow 1s cubic-bezier(.2,.8,.2,1) .4s backwards; }
    .ip-note { grid-column: 1 / -1; color: rgba(255,255,255,.6); font-size: 11px; line-height: 1.6; }

    /* NARA + aksi */
    .ip-nara { margin-bottom: 22px; background: linear-gradient(145deg, #f5f3ff, #fff 75%); }
    .ip-nara .ip-h { display: flex; align-items: center; margin-bottom: 14px; }
    .ip-live { width: 8px; height: 8px; margin-right: 8px; border-radius: 50%; background: #10b981; animation: ip-lv 2s infinite; }
    @keyframes ip-lv { 0% { box-shadow: 0 0 0 0 rgba(16,185,129,.5); } 70%, 100% { box-shadow: 0 0 0 8px rgba(16,185,129,0); } }
    .ip-act { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; padding-top: 22px; border-top: 1px solid rgba(255,255,255,.2); }
    .ip-act b { font-size: 14px; }
    .ip-act p { margin-top: 3px; color: rgba(255,255,255,.75); font-size: 12px; }
    .ip-btns { display: flex; flex-wrap: wrap; gap: 10px; }
    .ip-btn { display: inline-flex; align-items: center; justify-content: center; min-height: 44px; padding: 0 20px; border-radius: 13px; font-size: 12.5px; font-weight: 600; text-decoration: none; transition: transform .2s, background .2s; }
    .ip-btn:hover { transform: translateY(-2px); }
    .ip-btn:focus-visible { outline: 3px solid rgba(255,255,255,.7); outline-offset: 3px; }
    .ip-btn.w { color: var(--ink); background: #fff; box-shadow: 0 10px 24px rgba(10,6,60,.3); }
    .ip-btn.g { color: #fff; background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.34); }
    .ip-btn.g:hover { background: rgba(255,255,255,.25); }
    .ip-btn.p { margin-top: 16px; color: #fff; background: linear-gradient(135deg, var(--ink), var(--pri)); box-shadow: 0 10px 24px rgba(30,42,107,.3); }

    /* Kosong */
    .ip-empty { padding: 36px 24px; text-align: center; color: var(--mut); font-size: 13px; line-height: 1.8; }
    .ip-empty svg { width: 96px; height: 96px; margin-bottom: 10px; animation: ip-spin 40s linear infinite; }
    .ip-empty b { display: block; color: var(--ink); font-size: 17px; }
    @keyframes ip-spin { to { transform: rotate(360deg); } }

    /* Bidang yang akan dipetakan (hanya saat belum ada data) */
    .ip-sec { margin-top: 30px; }
    .ip-sec h2 { font-size: 17px; font-weight: 700; letter-spacing: -.01em; }
    .ip-sec > p { margin: 4px 0 16px; color: rgba(255,255,255,.72); font-size: 12px; line-height: 1.6; }
    .ip-ag { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 14px; }
    .ip-ar { padding: 20px; color: var(--tx); background: #fff; border-radius: 20px; box-shadow: 0 18px 46px rgba(10,6,60,.28); transition: transform .25s; }
    .ip-ar:hover { transform: translateY(-3px); }
    .ip-ai { width: 44px; height: 44px; display: grid; place-items: center; border-radius: 14px; color: var(--pri); background: linear-gradient(135deg, #eef2ff, #f3e8ff); }
    .ip-ai svg { width: 22px; height: 22px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
    .ip-ar b { display: block; margin-top: 14px; color: var(--ink); font-size: 14px; line-height: 1.35; }
    .ip-ar p { margin-top: 6px; color: var(--mut); font-size: 12px; line-height: 1.65; }
    .ip-ar small { display: inline-block; margin-top: 12px; padding: 3px 9px; border-radius: 99px; background: #f5f3ff; color: #5b5bd6; font-size: 10.5px; font-weight: 600; }

    /* Prospek karier */
    .ip-kg { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
    .ip-kc { display: flex; flex-direction: column; padding: 20px; color: var(--tx); background: #fff; border-radius: 20px; box-shadow: 0 18px 46px rgba(10,6,60,.28); transition: transform .25s; }
    .ip-kc:hover { transform: translateY(-3px); }
    .ip-kc.top { box-shadow: 0 0 0 2px #c4b5fd, 0 22px 50px rgba(10,6,60,.35); background: linear-gradient(180deg, #faf8ff, #fff 60%); }
    .ip-kh { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
    .ip-km { padding: 4px 10px; border-radius: 99px; background: #eef2ff; color: var(--pri); font-size: 11px; font-weight: 700; }
    .ip-kc.top .ip-km { color: #fff; background: linear-gradient(135deg, var(--pri), var(--vio)); }
    .ip-kc b { margin-top: 14px; color: var(--ink); font-size: 15px; line-height: 1.35; }
    .ip-kc p { margin-top: 6px; color: var(--mut); font-size: 12.5px; line-height: 1.7; }
    .ip-kt { display: flex; flex-wrap: wrap; gap: 6px; margin-top: auto; padding-top: 14px; }
    .ip-kt span { padding: 3px 9px; border-radius: 99px; background: #f1f5f9; color: #64748b; font-size: 10.5px; font-weight: 600; }
    .ip-kt span.pri { background: #f5f3ff; color: #5b5bd6; }
    .ip-sec > p.ip-kn { margin: 16px 0 0; color: rgba(255,255,255,.6); font-size: 11px; }

    /* Loading NARA */
    .ip-load { position: fixed; inset: 0; z-index: 100; display: grid; place-items: center; padding: 20px; background: rgba(27,23,96,.9); backdrop-filter: blur(14px); opacity: 0; visibility: hidden; transition: opacity .25s, visibility .25s; }
    .ip-load.on { opacity: 1; visibility: visible; }
    .ip-lc { width: min(420px, 100%); padding: 30px 28px; text-align: center; color: var(--tx); background: #fff; border-radius: 26px; box-shadow: 0 30px 80px rgba(10,6,60,.5); }
    .ip-lc img { width: 120px; height: 120px; object-fit: contain; animation: ip-bob 4s ease-in-out infinite; }
    .ip-lc h2 { margin-top: 4px; color: var(--ink); font-size: 22px; font-weight: 700; }
    .ip-lc p { max-width: 320px; margin: 6px auto 0; color: var(--mut); font-size: 12px; line-height: 1.7; }
    .ip-lbar { height: 6px; margin-top: 22px; overflow: hidden; border-radius: 99px; background: #eceaff; }
    .ip-lbar i { display: block; width: 0; height: 100%; border-radius: inherit; background: linear-gradient(90deg, var(--pri), var(--vio)); transition: width .8s ease; }
    .ip-lst { margin-top: 10px; color: var(--mut); font-size: 11.5px; font-weight: 500; }

    @media (max-width: 1000px) { .ip-ag { grid-template-columns: repeat(3, minmax(0, 1fr)); } .ip-kg { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 950px) { .ip-main, .ip-dna { grid-template-columns: 1fr; } }
    @media (max-width: 700px) {
        .ip { padding: 20px 12px 40px; }
        .ip-hero { display: block; }
        .ip-hero > div:first-child { padding-right: 100px; }
        .ip-hm { position: absolute; top: 0; right: 0; width: 104px; height: 104px; }
        .ip-hm img { width: 96px; height: 96px; }
        .ip-card { padding: 20px 16px; }
        .ip-dna { padding: 22px 18px; }
        .ip-dr { grid-template-columns: 110px minmax(0, 1fr); gap: 16px; }
        .ip-rw { width: 110px; height: 110px; }
        .ip-btns, .ip-btn { width: 100%; }
        .ip-ag { grid-template-columns: 1fr; gap: 10px; }
        .ip-kg { grid-template-columns: 1fr; gap: 10px; }
        .ip-ar { display: grid; grid-template-columns: 44px minmax(0, 1fr); column-gap: 14px; padding: 16px; }
        .ip-ai { grid-row: span 3; }
        .ip-ar b { margin-top: 0; }
        .ip-ar small { justify-self: start; }
    }

    /* Pusatkan konten loading NARA */
#naraLoadingScreen .ip-lc {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

/* Logo NARA tepat di tengah */
#naraLoadingScreen .ip-lc img {
    display: block;
    align-self: center;
    margin: 0 auto 18px;
    float: none;
}

/* Teks tetap rapi di tengah */
#naraLoadingScreen .ip-lc h2,
#naraLoadingScreen .ip-lc p,
#naraLoadingScreen .ip-lc .ip-lbar,
#naraLoadingScreen .ip-lc .ip-lst {
    align-self: center;
    text-align: center;
}

    @media (prefers-reduced-motion: reduce) { .ip *, .ip *::before, .ip *::after { animation: none !important; transition: none !important; } }
</style>

<div class="ip">
    <div class="ip-bg" aria-hidden="true"><i class="ip-o1"></i><i class="ip-o2"></i><div class="ip-grid-bg"></div></div>

    <div class="ip-wrap">

        <section class="ip-hero ip-fi">
            <div>
                <h1>Profil minat {{ $nama }}</h1>
                <p>Gambaran pola minatmu berdasarkan jawaban asesmen. ARAH memakai profil ini sebagai salah satu dasar untuk menyusun rekomendasi studi.</p>
            </div>
            <div class="ip-hm"><img src="{{ asset('images/happy.png') }}" alt="NARA"></div>
        </section>

        @if ($ada)

            <div class="ip-main">
                <section class="ip-card ip-fi" style="--i:1">
                    <h2 class="ip-h">Peta minat</h2>
                    <p class="ip-sub">Semakin jauh area dari titik tengah, semakin tinggi minatmu pada bidang itu.</p>
                    <svg id="ipRadar" class="ip-radar" viewBox="0 0 500 390" role="img" aria-label="Grafik radar minat akademik"></svg>
                </section>

                <section class="ip-card ip-fi" style="--i:2">
                    <h2 class="ip-h">Analisis minat</h2>
                    <p class="ip-sub">Skor setiap bidang dari asesmenmu.</p>
                    <div class="ip-top">
                        <small>Bidang paling menonjol</small>
                        <b>{{ $tertinggi->kategori }}</b>
                        <span>{{ round($tertinggi->skor) }}% tingkat minat</span>
                    </div>
                    @foreach ($daftar as $item)
                        <div class="ip-ir">
                            <div class="ip-nm">{{ $item->kategori }}</div>
                            <div class="ip-pc">{{ round($item->skor) }}%</div>
                            <div class="ip-tr"><i style="--w:{{ min(100, max(0, (float) $item->skor)) }}%"></i></div>
                        </div>
                    @endforeach
                </section>
            </div>

            <section class="ip-dna ip-fi" style="--i:3">
                <div>
                    <span class="ip-chip">Academic DNA</span>
                    <h2>{{ $dnaProfile['name'] }}</h2>
                    <p class="ip-dcat">Dominan pada {{ $tertinggi->kategori }}</p>
                    <p class="ip-dd">{{ $dnaProfile['description'] }}</p>
                    <div class="ip-traits">@foreach ($dnaProfile['traits'] as $trait)<span>{{ $trait }}</span>@endforeach</div>
                </div>
                <div class="ip-dr">
                    <div class="ip-rw">
                        <div class="ip-ring" style="--v:{{ $dna }}"></div>
                        <div class="ip-rn"><b>{{ $dnaIndex }}</b><small>DNA Index</small></div>
                    </div>
                    <div class="ip-mt">
                        <div>
                            <div class="ip-mh"><span>Minat</span><span>{{ $interestAverage }}%</span></div>
                            <div class="ip-mb"><i style="--w:{{ min(100, max(0, (float) $interestAverage)) }}%"></i></div>
                        </div>
                        <div>
                            <div class="ip-mh"><span>Kemampuan</span><span>{{ $abil > 0 ? round($abil).'%' : 'Belum tersedia' }}</span></div>
                            @if ($abil > 0)<div class="ip-mb"><i style="--w:{{ min(100, $abil) }}%"></i></div>@endif
                        </div>
                    </div>
                </div>
                <p class="ip-note">Academic DNA adalah ringkasan visual dari hasil asesmen minat dan data akademik di ARAH, bukan tes kepribadian atau penilaian psikologis.</p>
            </section>

            @stack('karier')

            <section class="ip-card ip-nara ip-fi" style="--i:4">
                <h2 class="ip-h"><span class="ip-live"></span>Insight dari NARA</h2>
                <x-nara-bubble :message="$naraInsight" />
            </section>

            <section class="ip-act ip-fi" style="--i:5">
                <div>
                    <b>Profil minatmu sudah selesai dianalisis.</b>
                    <p>Langkah berikutnya: lihat mata kuliah yang paling sesuai dengan profil akademikmu.</p>
                </div>
                <div class="ip-btns">
                    <a href="{{ route('assessment.show') }}" class="ip-btn g">Ulangi asesmen</a>
                    <a href="{{ route('recommendations.generate') }}" id="generateRecommendationBtn" class="ip-btn w">Lihat rekomendasi AI →</a>
                </div>
            </section>

        @else

            <section class="ip-card ip-empty ip-fi" style="--i:1">
                <svg viewBox="0 0 120 120" fill="none" stroke="#c4b5fd" stroke-width="1.5" aria-hidden="true"><polygon points="60,10 105,35 105,85 60,110 15,85 15,35"/><polygon points="60,30 87,45 87,75 60,90 33,75 33,45" stroke="#a78bfa"/><circle cx="60" cy="60" r="4" fill="#7C3AED" stroke="none"/></svg>
                <b>Profil minatmu belum terbentuk</b>
                Isi asesmen singkat berisi 20 pernyataan supaya NARA bisa memetakan minat dan kemampuanmu.<br>
                <a href="{{ route('assessment.show') }}" class="ip-btn p">Mulai asesmen</a>
            </section>

            @php
                $bidang = [
                    ['Pemrograman & Pengembangan', 'Membangun aplikasi, memakai logika dan algoritma, serta memperbaiki error.', '<path d="M8 8l-4 4 4 4M16 8l4 4-4 4M14 5l-4 14"/>'],
                    ['UI/UX & Kreativitas Digital', 'Merancang tampilan yang nyaman dipakai dan mencari ide kreatif produk digital.', '<rect x="3" y="4" width="18" height="16" rx="3"/><path d="M3 9h18M9 9v11"/>'],
                    ['Data & Analitik', 'Mengolah data, mencari pola, dan mengambil keputusan dari angka.', '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>'],
                    ['Sistem & Teknologi', 'Memahami jaringan, server, dan cara berbagai sistem saling terhubung.', '<rect x="3" y="4" width="18" height="6" rx="2"/><rect x="3" y="14" width="18" height="6" rx="2"/><path d="M7 7h.01M7 17h.01"/>'],
                    ['Bisnis & Manajemen Teknologi', 'Menghubungkan kebutuhan bisnis dengan solusi dan pengembangan teknologi.', '<rect x="3" y="7" width="18" height="13" rx="3"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 13h18"/>'],
                ];
            @endphp

            <section class="ip-sec ip-fi" style="--i:2">
                <h2>Lima bidang yang akan dipetakan</h2>
                <p>Setiap bidang diukur lewat 4 pernyataan di asesmen.</p>
                <div class="ip-ag">
                    @foreach ($bidang as $b)
                        <div class="ip-ar">
                            <span class="ip-ai"><svg viewBox="0 0 24 24" aria-hidden="true">{!! $b[2] !!}</svg></span>
                            <b>{{ $b[0] }}</b>
                            <p>{{ $b[1] }}</p>
                            <small>4 pernyataan</small>
                        </div>
                    @endforeach
                </div>
            </section>

            @stack('karier')

        @endif

    </div>
</div>

@if ($ada)
    <div id="naraLoadingScreen" class="ip-load" aria-live="polite">
        <div class="ip-lc">
            <img src="{{ asset('images/mikir.png') }}" alt="NARA">
            <h2>Sedang menganalisis</h2>
            <p>NARA sedang membaca profil akademikmu untuk menyiapkan rekomendasi yang relevan.</p>
            <div class="ip-lbar"><i id="naraLoadingBar"></i></div>
            <div class="ip-lst" id="naraLoadingText">Membaca profil minat...</div>
        </div>
    </div>

    <script>
    (function () {
        /* Radar: semua koordinat dihitung dari satu pusat supaya grid dan data selalu sejajar */
        var svg = document.getElementById('ipRadar');
        var raw = @json($radarScores);
        var data = Array.isArray(raw) ? raw : Object.keys(raw).map(function (k) { return raw[k]; });
        var names = ['Pemrograman', 'UI/UX', 'Data', 'Sistem', 'Bisnis'];
        var NS = 'http://www.w3.org/2000/svg', cx = 250, cy = 195, R = 140, n = names.length;

        function ang(i) { return (-90 + i * 360 / n) * Math.PI / 180; }
        function pt(i, r) { return [cx + Math.cos(ang(i)) * r, cy + Math.sin(ang(i)) * r]; }
        function el(tag, attrs) { var e = document.createElementNS(NS, tag); for (var k in attrs) e.setAttribute(k, attrs[k]); svg.appendChild(e); return e; }
        function ring(r) { return names.map(function (_, i) { return pt(i, r).join(','); }).join(' '); }

        [.25, .5, .75, 1].forEach(function (f) { el('polygon', { points: ring(R * f), 'class': 'g' }); });
        names.forEach(function (_, i) { var p = pt(i, R); el('line', { x1: cx, y1: cy, x2: p[0], y2: p[1], 'class': 'ax' }); });

        var sc = names.map(function (_, i) { var s = data[i] ? Number(data[i].skor) : 0; return Math.max(0, Math.min(100, s || 0)); });
        el('polygon', { points: sc.map(function (s, i) { return pt(i, R * s / 100).join(','); }).join(' '), 'class': 'ar' });
        sc.forEach(function (s, i) { var p = pt(i, R * s / 100); el('circle', { cx: p[0], cy: p[1], r: 5, 'class': 'pt' }); });
        names.forEach(function (nm, i) {
            var p = pt(i, R + 22), c = Math.cos(ang(i));
            var anchor = c > .3 ? 'start' : (c < -.3 ? 'end' : 'middle');
            el('text', { x: p[0], y: p[1], 'text-anchor': anchor, 'class': 'lb' }).textContent = nm;
            el('text', { x: p[0], y: p[1] + 15, 'text-anchor': anchor, 'class': 'lv' }).textContent = Math.round(sc[i]) + '%';
        });

        /* Layar loading sebelum membuka rekomendasi */
        var btn = document.getElementById('generateRecommendationBtn');
        var scr = document.getElementById('naraLoadingScreen');
        var bar = document.getElementById('naraLoadingBar');
        var st = document.getElementById('naraLoadingText');
        var steps = [[25, 'Membaca profil minat...'], [50, 'Mengevaluasi kemampuan akademik...'], [75, 'Mencocokkan mata kuliah...'], [100, 'Menyiapkan rekomendasi AI...']];
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            if (scr.classList.contains('on')) return;
            var url = this.href;
            scr.classList.add('on');
            steps.forEach(function (s, i) { setTimeout(function () { bar.style.width = s[0] + '%'; st.textContent = s[1]; }, i * 800); });
            setTimeout(function () { window.location.href = url; }, 3200);
        });
    })();
    </script>
@endif

@endsection