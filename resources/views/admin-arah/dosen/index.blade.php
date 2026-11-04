@extends('layouts.app')

@section('title', 'Dosen Global - Admin ARAH')

@section('content')

@php
    $ic = [
        'cap'    => '<path d="M12 4L2 9l10 5 10-5-10-5z"/><path d="M6 11v5c0 1.5 3 3 6 3s6-1.5 6-3v-5"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/>',
        'reset'  => '<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/>',
        'eye'    => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
        'back'   => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
        'go'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
    ];

    $pg = $dosen->hasPages() ? $dosen->appends(request()->except('page')) : $dosen;
@endphp

<style>
    .aa { --ink:#1E2A6B; --pri:#4F46E5; --tx:#334155; --mut:#64748b; --line:#e8e4ff; --soft:#f6f4ff;
        position: relative; overflow: hidden; min-height: calc(100vh - 70px); padding: 30px 22px 60px; color: var(--tx);
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; background: linear-gradient(165deg, #15174a 0%, #2f2a9b 50%, #8b2fc9 100%); }
    .aa *, .aa *::before, .aa *::after { box-sizing: border-box; }
    .aa-wrap { position: relative; z-index: 2; width: min(1280px, 100%); margin: 0 auto; }
    .aa .i { width: 20px; height: 20px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }

    /* latar bergerak */
    .aa-bg { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
    .aa-orb { position: absolute; border-radius: 50%; filter: blur(80px); animation: aa-dr 24s ease-in-out infinite alternate; }
    .aa-o1 { width: 500px; height: 500px; top: -170px; left: -140px; background: #7C3AED; opacity: .5; }
    .aa-o2 { width: 440px; height: 440px; bottom: -160px; right: -120px; background: #d946ef; opacity: .4; animation-direction: alternate-reverse; }
    @keyframes aa-dr { to { transform: translate(120px, 90px) scale(1.15); } }
    .aa-grid { position: absolute; inset: 0; background-size: 56px 56px; animation: aa-gm 40s linear infinite;
        background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
        -webkit-mask-image: linear-gradient(#000 20%, transparent 95%); mask-image: linear-gradient(#000 20%, transparent 95%); }
    @keyframes aa-gm { to { background-position: 0 56px, 56px 0; } }
    .aa-arw { position: absolute; bottom: -40px; left: var(--x); width: var(--s); height: var(--s); opacity: 0; animation: aa-up var(--d) linear infinite; animation-delay: var(--dl); }
    .aa-arw::before { content: ""; position: absolute; inset: 0; border-radius: 3px; border-top: 2.5px solid rgba(255,255,255,.6); border-left: 2.5px solid rgba(255,255,255,.6); transform: rotate(45deg) scale(.7); }
    @keyframes aa-up { 0% { transform: translateY(0); opacity: 0; } 12%, 88% { opacity: .45; } 100% { transform: translateY(-115vh); opacity: 0; } }

    /* hero + robot (tanpa lingkaran) */
    .aa-hero { display: grid; grid-template-columns: 1fr auto; align-items: center; gap: 24px; margin-bottom: 26px; padding: 14px 8px 8px; color: #fff; }
    .aa-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .aa-chip { display: inline-flex; align-items: center; gap: 7px; padding: 6px 12px; border-radius: 99px; background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.26); font-size: 11.5px; font-weight: 600; }
    .aa-chip .i { width: 14px; height: 14px; }
    .aa-dot { width: 8px; height: 8px; border-radius: 50%; background: #34d399; }
    .aa-dot--off { background: #f87171; }
    .aa-hero h1 { margin: 0 0 12px; font-size: clamp(28px, 4.4vw, 44px); line-height: 1.12; font-weight: 700; letter-spacing: -.035em; }
    .aa-hero p { margin: 0; max-width: 580px; color: rgba(255,255,255,.85); font-size: 14.5px; line-height: 1.75; }
    .aa-back { display: inline-flex; align-items: center; gap: 6px; margin-bottom: 14px; color: rgba(255,255,255,.8); font-size: 12px; text-decoration: none; }
    .aa-back .i { width: 14px; height: 14px; }
    .aa-back:hover { color: #fff; }
    .aa-hm { position: relative; width: 200px; height: 190px; display: grid; place-items: center; }
    .aa-hm img { width: 180px; height: 180px; object-fit: contain; filter: drop-shadow(0 18px 28px rgba(8,16,70,.45)); animation: aa-bob 4s ease-in-out infinite; }
    .aa-hm--s { width: 130px; height: 130px; }
    .aa-hm--s img { width: 120px; height: 120px; }
    @keyframes aa-bob { 0%, 100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-10px) rotate(2deg); } }

    /* tombol */
    .aa-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 44px; padding: 0 20px; border: 0; border-radius: 14px; font-family: inherit; font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer; transition: transform .2s, box-shadow .2s; }
    .aa-btn .i { width: 17px; height: 17px; }
    .aa-btn:hover { transform: translateY(-2px); }
    .aa-hero .aa-btn { margin-top: 22px; }
    .aa-btn--w { background: #fff; color: var(--pri); box-shadow: 0 12px 26px rgba(8,16,70,.35); }
    .aa-btn--p { background: linear-gradient(135deg, #1E2A6B, #4F46E5); color: #fff; box-shadow: 0 12px 26px rgba(79,70,229,.35); }
    .aa-btn--g { background: var(--soft); color: var(--ink); border: 1px solid var(--line); }
    .aa-btn:focus-visible, .aa-act:focus-visible, .aa-mini:focus-visible { outline: 3px solid #a5b4fc; outline-offset: 2px; }

    /* kartu + statistik */
    .aa-card { background: #fff; border: 1px solid rgba(255,255,255,.7); border-radius: 22px; box-shadow: 0 18px 46px rgba(8,16,70,.3); animation: aa-in .6s ease both; }
    @keyframes aa-in { from { opacity: 0; transform: translateY(16px); } }
    .aa-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-bottom: 20px; }
    .aa-stat { padding: 20px; transition: transform .25s, box-shadow .25s; }
    .aa-stat:hover { transform: translateY(-4px); box-shadow: 0 26px 56px rgba(8,16,70,.38); }
    .aa-ico { width: 46px; height: 46px; display: grid; place-items: center; border-radius: 15px; color: var(--pri); background: linear-gradient(135deg, #eef2ff, #f3e8ff); transition: transform .3s; }
    .aa-stat:hover .aa-ico { transform: rotate(-8deg) scale(1.08); }
    .aa-lb { margin-top: 14px; color: var(--mut); font-size: 12px; font-weight: 500; }
    .aa-val { margin-top: 2px; color: var(--ink); font-size: 32px; line-height: 1.2; font-weight: 700; letter-spacing: -.03em; }
    .aa-nt { margin-top: 2px; color: #94a3b8; font-size: 11px; }

    .aa-cols { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; align-items: stretch; }
    .aa-pn { padding: 24px; }
    .aa-h { margin: 0; color: var(--ink); font-size: 18px; font-weight: 700; letter-spacing: -.01em; }
    .aa-sub { margin: 4px 0 18px; color: var(--mut); font-size: 12.5px; }

    /* baris menu / aksi */
    .aa-acts { display: grid; gap: 10px; }
    .aa-acts--2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .aa-act { display: flex; align-items: center; gap: 14px; padding: 13px 14px; border: 1px solid var(--line); border-radius: 16px; background: #fff; color: inherit; text-decoration: none; transition: transform .2s, box-shadow .2s, border-color .2s, background .2s; }
    .aa-act .aa-ico { width: 42px; height: 42px; border-radius: 13px; }
    .aa-tx { flex: 1; min-width: 0; }
    .aa-act b { display: block; color: var(--ink); font-size: 13.5px; }
    .aa-act small { display: block; color: var(--mut); font-size: 11.5px; line-height: 1.5; }
    .aa-cnt { display: block; margin-top: 4px; color: var(--pri); font-size: 11.5px; font-weight: 600; }
    .aa-go { color: #a5b4fc; transition: transform .2s, color .2s; }
    .aa-act[href]:hover { transform: translateX(4px); border-color: #c7c2ff; background: #faf9ff; box-shadow: 0 10px 24px rgba(79,70,229,.14); }
    .aa-act[href]:hover .aa-go { color: var(--pri); transform: translateX(3px); }

    /* info kampus */
    .aa-info { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
    .aa-it { display: flex; gap: 12px; padding: 14px; border: 1px solid var(--line); border-radius: 16px; background: var(--soft); }
    .aa-it .aa-ico { width: 38px; height: 38px; border-radius: 12px; background: #fff; box-shadow: 0 4px 10px rgba(79,70,229,.12); }
    .aa-it .i { width: 18px; height: 18px; }
    .aa-k { color: #94a3b8; font-size: 11px; }
    .aa-v { margin-top: 3px; color: var(--ink); font-size: 13.5px; font-weight: 600; line-height: 1.45; overflow-wrap: anywhere; }
    .aa-v.none { color: #b6bfcd; font-weight: 400; }

    /* tabel */
    .aa-tb { overflow: hidden; }
    .aa-table { width: 100%; border-collapse: collapse; }
    .aa-table th { padding: 14px 20px; background: var(--soft); border-bottom: 1px solid var(--line); color: var(--mut); font-size: 12px; font-weight: 600; text-align: left; }
    .aa-table td { padding: 16px 20px; border-top: 1px solid var(--line); color: var(--tx); font-size: 12.5px; vertical-align: middle; }
    .aa-table tbody tr:first-child td { border-top: 0; }
    .aa-table tbody tr { transition: background .2s; }
    .aa-table tbody tr:hover { background: #faf9ff; }
    .aa-table td.aa-empty { padding: 48px 20px; text-align: center; color: #94a3b8; font-size: 13px; }
    .aa-empty img { display: block; width: 96px; height: 96px; margin: 0 auto 10px; object-fit: contain; }
    .aa-c { text-align: center !important; }
    .aa-r { text-align: right !important; }
    .aa-nm { display: flex; align-items: center; gap: 12px; }
    .aa-nm .aa-ico { width: 40px; height: 40px; border-radius: 13px; }
    .aa-nm b { display: block; color: var(--ink); font-size: 13.5px; }
    .aa-nm small { color: #94a3b8; font-size: 11.5px; }
    .aa-code { color: var(--ink); font-weight: 600; }
    .aa-bd { display: inline-flex; align-items: center; gap: 6px; padding: 5px 11px; border-radius: 99px; font-size: 11px; font-weight: 600; }
    .aa-bd::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .aa-bd--on { background: #ecfdf5; color: #047857; }
    .aa-bd--off { background: #fef2f2; color: #b91c1c; }
    .aa-acs { display: inline-flex; align-items: center; gap: 8px; }
    .aa-mini { display: inline-flex; align-items: center; gap: 6px; min-height: 36px; padding: 0 12px; border: 1px solid var(--line); border-radius: 11px; background: #fff; color: var(--ink); font-family: inherit; font-size: 11.5px; font-weight: 600; text-decoration: none; cursor: pointer; transition: background .2s, border-color .2s; }
    .aa-mini .i { width: 15px; height: 15px; }
    .aa-mini:hover { background: var(--soft); border-color: #c7c2ff; }
    .aa-mini--r { background: #fff5f5; border-color: #fecaca; color: #dc2626; }
    .aa-mini--g { background: #f0fdf8; border-color: #a7f3d0; color: #059669; }

    .aa-fl { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; padding: 12px 16px; border-radius: 14px; background: rgba(255,255,255,.96); color: #047857; font-size: 13px; font-weight: 500; box-shadow: 0 10px 24px rgba(8,16,70,.2); }
    .aa-fl--e { color: #b91c1c; }

    /* form */
    .aa-fm { padding: 26px; }
    .aa-fs { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
    .aa-full { grid-column: 1 / -1; }
    .aa-lbl { display: flex; align-items: center; gap: 7px; margin-bottom: 8px; color: var(--ink); font-size: 12.5px; font-weight: 600; }
    .aa-lbl .i { width: 16px; height: 16px; color: var(--pri); }
    .aa-in { width: 100%; min-height: 48px; padding: 12px 16px; border: 1px solid var(--line); border-radius: 14px; background-color: var(--soft); color: var(--ink); font-family: inherit; font-size: 14px; outline: 0; transition: border-color .2s, box-shadow .2s, background-color .2s; }
    .aa-in::placeholder { color: #a3adc2; }
    .aa-in:focus { border-color: #a5b4fc; background-color: #fff; box-shadow: 0 0 0 4px rgba(79,70,229,.14); }
    textarea.aa-in { min-height: 112px; resize: vertical; }
    .aa-err { margin-top: 6px; color: #dc2626; font-size: 12px; }
    .aa-ft { display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--line); }
    .aa-seg { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
    .aa-seg label { position: relative; cursor: pointer; }
    .aa-seg input { position: absolute; inset: 0; margin: 0; opacity: 0; cursor: pointer; }
    .aa-seg span { display: flex; align-items: center; justify-content: center; min-height: 48px; border: 1px solid var(--line); border-radius: 14px; background: var(--soft); color: var(--mut); font-size: 13px; font-weight: 600; transition: background .2s, color .2s, box-shadow .2s; }
    .aa-seg input:checked + span { border-color: transparent; background: linear-gradient(135deg, #4F46E5, #7C3AED); color: #fff; box-shadow: 0 10px 22px rgba(79,70,229,.3); }
    .aa-seg label:last-child input:checked + span { background: linear-gradient(135deg, #be123c, #e11d48); box-shadow: 0 10px 22px rgba(225,29,72,.28); }
    .aa-seg input:focus-visible + span { outline: 3px solid #a5b4fc; outline-offset: 2px; }

    @media (max-width: 1000px) { .aa-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } .aa-cols { grid-template-columns: 1fr; } }
    @media (max-width: 760px) {
        .aa { padding: 16px 12px 36px; }
        .aa-hero { grid-template-columns: 1fr; padding: 6px 0 0; }
        .aa-hm, .aa-hm--s { position: absolute; top: 4px; right: 0; width: 96px; height: 96px; }
        .aa-hm img, .aa-hm--s img { width: 78px; height: 78px; }
        .aa-hero > div:first-child { padding-right: 84px; }
        .aa-info, .aa-acts--2, .aa-fs { grid-template-columns: 1fr; }
        .aa-pn, .aa-fm { padding: 18px; }
        .aa-ft { flex-direction: column-reverse; }
        .aa-ft .aa-btn { width: 100%; }

        .aa-table thead { display: none; }
        .aa-table, .aa-table tbody, .aa-table tr { display: block; width: 100%; }
        .aa-table tr { padding: 14px 16px; border-top: 1px solid var(--line); }
        .aa-table tbody tr:first-child { border-top: 0; }
        .aa-table td { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 6px 0; border: 0; text-align: right !important; }
        .aa-table td::before { content: attr(data-label); color: #94a3b8; font-size: 11.5px; font-weight: 500; text-align: left; }
        .aa-table td.aa-first, .aa-table td.aa-end, .aa-table td.aa-empty { display: block; text-align: left !important; }
        .aa-table td.aa-empty { text-align: center !important; }
        .aa-table td.aa-first::before, .aa-table td.aa-end::before, .aa-table td.aa-empty::before { display: none; }
        .aa-table td.aa-end { padding-top: 12px; }
        .aa-acs { flex-wrap: wrap; }
    }
    @media (max-width: 460px) { .aa-stats { gap: 9px; } .aa-stat { padding: 14px; } .aa-val { font-size: 26px; } }
    @media (prefers-reduced-motion: reduce) { .aa *, .aa *::before, .aa *::after { animation: none !important; transition: none !important; } }

    /* ---- mahasiswa: filter, hasil, paginasi, detail ---- */
    .aa-flt { padding: 20px; margin-bottom: 18px; }
    .aa-fg { display: grid; grid-template-columns: minmax(240px, 1fr) minmax(200px, 280px) auto; gap: 14px; align-items: end; }
    .aa-fb { display: flex; gap: 10px; }
    .aa-fb .aa-btn { min-height: 48px; }
    .aa-sw { position: relative; }
    .aa-sw .i { position: absolute; left: 14px; top: 50%; width: 17px; height: 17px; margin-top: -8.5px; color: #94a3b8; pointer-events: none; }
    .aa-sw .aa-in { padding-left: 42px; }
    .aa-sel { -webkit-appearance: none; appearance: none; padding-right: 42px; background-repeat: no-repeat; background-position: right 14px center; background-size: 18px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); }

    .aa-ch { display: flex; justify-content: space-between; align-items: center; gap: 15px; padding: 20px 22px; border-bottom: 1px solid var(--line); }
    .aa-ch .aa-sub { margin: 4px 0 0; }
    .aa-sc { overflow-x: auto; }
    .aa-sc .aa-table { min-width: 900px; }
    .aa-av { width: 40px; height: 40px; display: grid; place-items: center; flex-shrink: 0; border-radius: 13px; background: linear-gradient(135deg, #4F46E5, #7C3AED); color: #fff; font-size: 15px; font-weight: 700; }
    .aa-av--l { width: 64px; height: 64px; border-radius: 20px; font-size: 24px; }
    .aa-ipk { color: var(--ink); font-weight: 700; }
    .aa-mu { color: #94a3b8; }
    .aa-grade { display: inline-block; min-width: 34px; padding: 4px 10px; border-radius: 10px; background: #eef2ff; color: var(--pri); font-size: 12.5px; font-weight: 700; text-align: center; }
    .aa-gs { display: block; margin-top: 3px; color: #94a3b8; font-size: 11px; }

    .aa-pg { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; padding: 16px 22px; border-top: 1px solid var(--line); }
    .aa-pg > span { color: var(--mut); font-size: 12px; }
    .aa-pg > div { display: flex; gap: 8px; }
    .aa-mini--off { opacity: .45; pointer-events: none; }

    .aa-prof { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; }
    .aa-info--3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .aa-mb { margin-bottom: 18px; }
    .aa-box { margin-bottom: 12px; padding: 4px 16px; border: 1px solid var(--line); border-radius: 16px; background: var(--soft); }
    .aa-box:last-child { margin-bottom: 0; }
    .aa-kv { display: flex; justify-content: space-between; gap: 20px; padding: 10px 0; border-bottom: 1px solid var(--line); font-size: 12px; }
    .aa-kv:last-child { border-bottom: 0; }
    .aa-kv span:first-child { color: var(--mut); }
    .aa-kv span:last-child { color: var(--ink); font-weight: 600; text-align: right; overflow-wrap: anywhere; }
    .aa-nil { padding: 32px 10px; text-align: center; color: #94a3b8; font-size: 13px; }
    .aa-card > .aa-nil { padding: 40px 20px; }

    @media (max-width: 1000px) {
        .aa-info--3 { grid-template-columns: 1fr; }
        .aa-fg { grid-template-columns: 1fr 1fr; }
        .aa-fg > div:first-child { grid-column: 1 / -1; }
    }
    @media (max-width: 760px) {
        .aa-fg { grid-template-columns: 1fr; }
        .aa-fb .aa-btn { flex: 1; }
        .aa-ch { padding: 16px; }
        .aa-pg { padding: 14px 16px; }
        .aa-sc { overflow: visible; }
        .aa-sc .aa-table { min-width: 0; }
        .aa-table tr { display: flex; flex-direction: column; }
        .aa-table td.aa-first { order: -1; }
    }
</style>

<div class="aa">

    <div class="aa-bg" aria-hidden="true">
        <div class="aa-grid"></div><i class="aa-orb aa-o1"></i><i class="aa-orb aa-o2"></i>
        <i class="aa-arw" style="--x:8%;  --s:22px; --d:18s; --dl:-3s"></i>
        <i class="aa-arw" style="--x:64%; --s:26px; --d:20s; --dl:-6s"></i>
        <i class="aa-arw" style="--x:92%; --s:20px; --d:24s; --dl:-14s"></i>
    </div>

    <div class="aa-wrap">

        <section class="aa-hero">
            <div>
                <div class="aa-chips">
                    <span class="aa-chip"><svg class="i" viewBox="0 0 24 24">{!! $ic['cap'] !!}</svg>{{ number_format($dosen->total()) }} dosen</span>
                </div>
                <h1>Dosen</h1>
                <p>Monitoring seluruh Dosen PA dari semua kampus yang terhubung dengan ARAH.</p>
            </div>
            <div class="aa-hm aa-hm--s"><img src="{{ asset('images/senyum.png') }}" alt="NARA" onerror="this.style.display='none'"></div>
        </section>

        {{-- FILTER --}}
        <form class="aa-card aa-flt" method="GET" action="{{ route('admin-arah.dosen.index') }}">
            <div class="aa-fg">

                <div>
                    <label class="aa-lbl" for="search">Cari dosen</label>
                    <div class="aa-sw">
                        <svg class="i" viewBox="0 0 24 24">{!! $ic['search'] !!}</svg>
                        <input class="aa-in" id="search" type="text" name="search" value="{{ $search }}" placeholder="Nama, NIP, atau email...">
                    </div>
                </div>

                <div>
                    <label class="aa-lbl" for="kampus_id">Kampus</label>
                    <select class="aa-in aa-sel" id="kampus_id" name="kampus_id">
                        <option value="">Semua Kampus</option>
                        @foreach ($kampus as $item)
                            <option value="{{ $item->id }}" @selected((string) $kampusId === (string) $item->id)>{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="aa-fb">
                    <button type="submit" class="aa-btn aa-btn--p">
                        <svg class="i" viewBox="0 0 24 24">{!! $ic['search'] !!}</svg>Cari
                    </button>
                    <a class="aa-btn aa-btn--g" href="{{ route('admin-arah.dosen.index') }}">
                        <svg class="i" viewBox="0 0 24 24">{!! $ic['reset'] !!}</svg>Reset
                    </a>
                </div>

            </div>
        </form>

        {{-- TABEL --}}
        <div class="aa-card aa-tb">

            <div class="aa-ch">
                <div>
                    <h2 class="aa-h">Daftar dosen</h2>
                    <p class="aa-sub">Menampilkan {{ $dosen->firstItem() ?? 0 }} - {{ $dosen->lastItem() ?? 0 }} dari {{ $dosen->total() }} dosen</p>
                </div>
            </div>

            <div class="aa-sc">
                <table class="aa-table">

                    <thead>
                        <tr>
                            <th>NIP</th>
                            <th>Dosen</th>
                            <th>Kampus</th>
                            <th class="aa-c">Mahasiswa</th>
                            <th class="aa-r">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($dosen as $item)
                            <tr>
                                <td class="aa-code" data-label="NIP">{{ $item->nip }}</td>

                                <td class="aa-first">
                                    <div class="aa-nm">
                                        <span class="aa-av">{{ strtoupper(mb_substr($item->nama, 0, 1)) }}</span>
                                        <div>
                                            <b>{{ $item->nama }}</b>
                                            <small>{{ $item->user->email ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Kampus">
                                    <b class="aa-code">{{ $item->kampus->kode ?? '-' }}</b>
                                    <small class="aa-gs">{{ $item->kampus->nama ?? '-' }}</small>
                                </td>

                                <td class="aa-c" data-label="Mahasiswa"><span class="aa-grade">{{ $item->mahasiswa_count }}</span></td>

                                <td class="aa-r aa-end">
                                    <a class="aa-mini" href="{{ route('admin-arah.dosen.show', $item->id) }}">
                                        <svg class="i" viewBox="0 0 24 24">{!! $ic['eye'] !!}</svg>Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="aa-empty" colspan="5">
                                    <img src="{{ asset('images/mikir.png') }}" alt="" onerror="this.style.display='none'">
                                    Tidak ada dosen ditemukan. Coba ubah kata pencarian atau filter kampus.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            {{-- PAGINASI --}}
            @if ($dosen->hasPages())
                <div class="aa-pg">
                    <span>Halaman {{ $pg->currentPage() }} dari {{ $pg->lastPage() }}</span>
                    <div>
                        @if ($pg->onFirstPage())
                            <span class="aa-mini aa-mini--off"><svg class="i" viewBox="0 0 24 24">{!! $ic['back'] !!}</svg>Sebelumnya</span>
                        @else
                            <a class="aa-mini" href="{{ $pg->previousPageUrl() }}"><svg class="i" viewBox="0 0 24 24">{!! $ic['back'] !!}</svg>Sebelumnya</a>
                        @endif

                        @if ($pg->hasMorePages())
                            <a class="aa-mini" href="{{ $pg->nextPageUrl() }}">Berikutnya<svg class="i" viewBox="0 0 24 24">{!! $ic['go'] !!}</svg></a>
                        @else
                            <span class="aa-mini aa-mini--off">Berikutnya<svg class="i" viewBox="0 0 24 24">{!! $ic['go'] !!}</svg></span>
                        @endif
                    </div>
                </div>
            @endif

        </div>

    </div>
</div>

@endsection