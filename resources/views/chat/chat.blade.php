@extends('layouts.app')

@section('title', 'Tanya NARA — ARAH')

@section('content')

@php
    $nama = explode(' ', trim(auth()->user()->name))[0];
    $prompts = [
        ['Analisis kondisi akademik', 'Pahami IPK dan nilai-nilaimu', 'Bantu aku memahami kondisi akademikku.', '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>'],
        ['Rekomendasi mata kuliah', 'Sesuai minat dan kemampuanmu', 'Mata kuliah apa yang cocok berdasarkan minat dan kemampuanku?', '<path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z"/>'],
        ['Rencana studi', 'Susun semester berikutnya', 'Bantu aku membuat rencana studi untuk semester berikutnya.', '<path d="M9 4l6 2 5-2v14l-5 2-6-2-5 2V6l5-2zM9 4v14M15 6v14"/>'],
        ['Persiapan perwalian', 'Bahan diskusi dengan Dosen PA', 'Apa yang harus aku diskusikan dengan Dosen PA?', '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 11l2 2 4-4"/>'],
    ];
    $groups = ['Hari ini' => [], 'Kemarin' => [], '7 hari terakhir' => [], 'Lebih lama' => []];
    foreach ($conversations as $c) {
        $k = $c->updated_at->isToday() ? 'Hari ini' : ($c->updated_at->isYesterday() ? 'Kemarin' : ($c->updated_at->gt(now()->subDays(7)) ? '7 hari terakhir' : 'Lebih lama'));
        $groups[$k][] = $c;
    }
    $pen = '<path d="M4 20h4L19 9l-4-4L4 16v4zM13.5 6.5l4 4"/>';
    $trash = '<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>';
@endphp

<style>
    .nr { --ink:#1E2A6B; --pri:#4F46E5; --vio:#7C3AED; --tx:#334155; --mut:#64748b; --line:#e5e3ff;
        position: relative; overflow: hidden; height: calc(100vh - 64px); height: calc(100dvh - 64px); padding: 18px 22px;
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; background: linear-gradient(165deg, #1b1760 0%, #3a2ea6 50%, #6b35e0 100%); }
    .nr *, .nr *::before, .nr *::after { box-sizing: border-box; }
    :where(.nr) h1, :where(.nr) p { margin: 0; }
    .nr-bg { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
    .nr-o1, .nr-o2 { position: absolute; border-radius: 50%; filter: blur(80px); animation: nr-dr 28s ease-in-out infinite alternate; }
    .nr-o1 { width: 460px; height: 460px; top: -150px; left: -120px; background: #8b5cf6; opacity: .5; }
    .nr-o2 { width: 400px; height: 400px; bottom: -140px; right: -120px; background: #c026d3; opacity: .26; animation-duration: 32s; }
    @keyframes nr-dr { to { transform: translate(100px, 70px) scale(1.1); } }
    .nr-gridbg { position: absolute; inset: 0; background-size: 56px 56px; -webkit-mask-image: linear-gradient(#000 20%, transparent 95%); mask-image: linear-gradient(#000 20%, transparent 95%);
        background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px); }
    @keyframes nr-up { from { opacity: 0; transform: translateY(16px); } }
    @keyframes nr-bob { 0%, 100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-8px) rotate(2deg); } }
    .nr svg.i { width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }
    .nr button:focus-visible, .nr textarea:focus-visible, .nr a:focus-visible, .nr input:focus-visible { outline: 3px solid #c4b5fd; outline-offset: 2px; }

    /* Cangkang: sidebar + chat */
    .nr-shell { position: relative; z-index: 1; display: grid; grid-template-columns: 290px minmax(0, 1fr); grid-template-rows: minmax(0, 1fr); height: 100%; max-width: 1180px; margin: 0 auto; overflow: hidden; background: #fff; border-radius: 26px; box-shadow: 0 30px 70px rgba(10,6,60,.4); transition: grid-template-columns .3s ease; animation: nr-up .6s ease backwards; }
    .nr-shell.sb-closed { grid-template-columns: 0 minmax(0, 1fr); }
    .nr-main { display: flex; flex-direction: column; min-width: 0; min-height: 0; height: 100%; }
    .nr-scrim { display: none; position: absolute; inset: 0; z-index: 5; background: rgba(10,6,60,.4); }

    /* Sidebar riwayat */
    .nr-side { min-width: 0; overflow: hidden; background: #f7f5ff; border-right: 1px solid var(--line); }
    .sd-in { display: flex; flex-direction: column; width: 290px; height: 100%; min-height: 0; }
    .sd-top { padding: 16px 14px 8px; }
    .sd-new { display: flex; align-items: center; justify-content: center; gap: 8px; min-height: 44px; border-radius: 14px; color: #fff; background: linear-gradient(135deg, var(--ink), var(--pri)); font: 600 12.5px 'Poppins', sans-serif; text-decoration: none; box-shadow: 0 8px 18px rgba(30,42,107,.25); transition: transform .2s; }
    .sd-new:hover { transform: translateY(-2px); }
    .sd-sw { position: relative; margin-top: 10px; }
    .sd-sw svg { position: absolute; left: 11px; top: 11px; width: 16px; height: 16px; color: #a3a3c2; }
    .sd-sw input { width: 100%; height: 38px; padding: 0 12px 0 34px; border: 1px solid var(--line); border-radius: 11px; background: #fff; color: var(--tx); font: 12px 'Poppins', sans-serif; }
    .sd-list { flex: 1; min-height: 0; overflow-y: auto; padding: 2px 10px 14px; }
    .sd-list::-webkit-scrollbar { width: 5px; }
    .sd-list::-webkit-scrollbar-thumb { background: #ddd6fe; border-radius: 9px; }
    .sd-g { margin: 14px 8px 6px; color: #94a3b8; font-size: 10.5px; font-weight: 600; }
    .sd-i { display: flex; align-items: center; border-radius: 11px; transition: background .2s, box-shadow .2s; animation: nr-up .3s ease backwards; }
    .sd-i:hover { background: #ede9fe; }
    .sd-i.on { background: #fff; box-shadow: 0 4px 12px rgba(79,70,229,.12); }
    .sd-t { flex: 1; min-width: 0; padding: 10px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; color: var(--tx); font-size: 12.5px; text-decoration: none; }
    .sd-i.on .sd-t { color: var(--ink); font-weight: 600; }
    .sd-a { display: none; gap: 2px; padding-right: 6px; }
    .sd-i:hover .sd-a, .sd-i.on .sd-a { display: flex; }
    .sd-a button { width: 26px; height: 26px; display: grid; place-items: center; border: 0; border-radius: 8px; background: none; color: var(--mut); cursor: pointer; }
    .sd-a button:hover { background: #fff; color: var(--pri); }
    .sd-a button[data-act="del"]:hover { color: #dc2626; }
    .sd-a svg.i { width: 14px; height: 14px; }
    .sd-empty { padding: 22px 12px; text-align: center; color: #94a3b8; font-size: 12px; line-height: 1.7; }

    /* Header */
    .nr-head { flex: 0 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 22px; border-bottom: 1px solid var(--line); }
    .nr-hl { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .nr-av { width: 44px; height: 44px; flex-shrink: 0; display: grid; place-items: center; border-radius: 14px; background: linear-gradient(135deg, #eef2ff, #f3e8ff); }
    .nr-av img { width: 36px; height: 36px; object-fit: contain; }
    .nr-nm { color: var(--ink); font-size: 16px; font-weight: 700; }
    .nr-st { display: flex; align-items: center; gap: 6px; margin-top: 2px; color: var(--mut); font-size: 11.5px; }
    .nr-dot { width: 7px; height: 7px; border-radius: 50%; background: #10b981; animation: nr-pulse 2s infinite; }
    @keyframes nr-pulse { 0% { box-shadow: 0 0 0 0 rgba(16,185,129,.5); } 70%, 100% { box-shadow: 0 0 0 8px rgba(16,185,129,0); } }
    .nr-ic { width: 38px; height: 38px; flex-shrink: 0; display: grid; place-items: center; border: 1px solid var(--line); border-radius: 12px; background: #fff; color: var(--mut); cursor: pointer; transition: color .2s, border-color .2s, background .2s; }
    .nr-ic:hover { color: var(--pri); border-color: #c4b5fd; background: #faf8ff; }
    .nr-ic .off { display: none; }
    .nr-ic.muted .on { display: none; }
    .nr-ic.muted .off { display: block; }

    /* Pesan */
    .nr-msgs { flex: 1; min-height: 0; overflow-y: auto; padding: 24px 28px; background: #fbfaff; }
    .nr-msgs::-webkit-scrollbar { width: 6px; }
    .nr-msgs::-webkit-scrollbar-thumb { background: #ddd6fe; border-radius: 10px; }
    .nr-m { display: flex; gap: 10px; max-width: 820px; margin: 0 auto 20px; }
    .nr-m.u { justify-content: flex-end; }
    .nr-sa { flex: 0 0 32px; width: 32px; height: 32px; display: grid; place-items: center; border-radius: 11px; background: linear-gradient(135deg, #eef2ff, #f3e8ff); }
    .nr-sa img { width: 26px; height: 26px; object-fit: contain; }
    .nr-bw { max-width: min(82%, 680px); }
    .nr-who { margin: 0 0 5px 3px; color: #94a3b8; font-size: 11px; font-weight: 600; }
    .nr-b { padding: 12px 16px; font-size: 13.5px; line-height: 1.75; white-space: pre-wrap; overflow-wrap: anywhere; border-radius: 18px; }
    .nr-b strong { color: var(--ink); }
    .nr-b code { padding: 1px 6px; border-radius: 6px; background: #f3e8ff; font-size: 12px; }
    .a .nr-b { color: var(--tx); background: #fff; border: 1px solid var(--line); border-top-left-radius: 6px; box-shadow: 0 4px 14px rgba(79,70,229,.06); }
    .u .nr-b { color: #fff; background: linear-gradient(135deg, var(--pri), var(--vio)); border-top-right-radius: 6px; box-shadow: 0 10px 22px rgba(79,70,229,.28); }
    .a .nr-b.err { color: #b91c1c; background: #fef2f2; border-color: #fecaca; }
    .nr-b.caret::after { content: ""; display: inline-block; width: 2px; height: 1em; margin-left: 2px; vertical-align: -2px; background: var(--pri); animation: nr-blink 1s steps(1) infinite; }
    @keyframes nr-blink { 50% { opacity: 0; } }
    .nr-m.new.a { animation: nr-ia .4s cubic-bezier(.2,.8,.2,1) backwards; }
    .nr-m.new.u { animation: nr-iu .35s cubic-bezier(.2,.8,.2,1) backwards; }
    @keyframes nr-ia { from { opacity: 0; transform: translateX(-14px) scale(.97); } }
    @keyframes nr-iu { from { opacity: 0; transform: translateX(14px) scale(.97); } }
    .nr-ty { display: none; gap: 10px; max-width: 820px; margin: 0 auto 18px; }
    .nr-ty.on { display: flex; animation: nr-ia .35s ease; }
    .nr-tb { display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: #fff; border: 1px solid var(--line); border-radius: 18px; border-top-left-radius: 6px; }
    .nr-dots { display: flex; gap: 4px; }
    .nr-dots i { width: 7px; height: 7px; border-radius: 50%; background: var(--vio); animation: nr-dot 1.1s infinite; }
    .nr-dots i:nth-child(2) { animation-delay: .15s; }
    .nr-dots i:nth-child(3) { animation-delay: .3s; }
    @keyframes nr-dot { 0%, 60%, 100% { transform: translateY(0); opacity: .35; } 30% { transform: translateY(-5px); opacity: 1; } }
    .nr-tt { color: var(--mut); font-size: 12px; transition: opacity .22s; }

    /* Sambutan */
    .nr-wel { min-height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 8px 0; }
    .nr-wel > * { animation: nr-up .55s ease calc(var(--i, 0) * 90ms) backwards; }
    .nr-wel img { width: 140px; height: 140px; object-fit: contain; filter: drop-shadow(0 16px 24px rgba(79,70,229,.3)); animation: nr-up .55s ease backwards, nr-bob 4s ease-in-out .6s infinite; }
    .nr-wel h1 { margin-top: 6px; color: var(--ink); font-size: clamp(24px, 3.4vw, 32px); font-weight: 700; letter-spacing: -.03em; }
    .nr-wel h1 em { font-style: normal; color: var(--vio); }
    .nr-wel p { max-width: 460px; margin-top: 8px; color: var(--mut); font-size: 13px; line-height: 1.75; }
    .nr-cards { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; width: min(580px, 100%); margin-top: 22px; }
    .nr-card { display: flex; align-items: center; gap: 12px; padding: 14px; text-align: left; color: var(--ink); background: #fff; border: 1px solid var(--line); border-radius: 16px; font: 600 12.5px 'Poppins', sans-serif; cursor: pointer; transition: transform .2s, border-color .2s, box-shadow .2s; }
    .nr-card:hover { transform: translateY(-3px); border-color: #c4b5fd; box-shadow: 0 12px 26px rgba(79,70,229,.14); }
    .nr-ci { width: 38px; height: 38px; flex-shrink: 0; display: grid; place-items: center; border-radius: 12px; color: var(--pri); background: linear-gradient(135deg, #eef2ff, #f3e8ff); transition: transform .25s; }
    .nr-card:hover .nr-ci { transform: rotate(-8deg) scale(1.08); }
    .nr-card small { display: block; margin-top: 2px; color: var(--mut); font-size: 11px; font-weight: 400; }

    /* Chip + input */
    .nr-chips { display: none; flex: 0 0 auto; gap: 8px; padding: 10px 22px 0; overflow-x: auto; scrollbar-width: none; background: #fff; border-top: 1px solid var(--line); }
    .nr-chips.show { display: flex; }
    .nr-chips::-webkit-scrollbar { display: none; }
    .nr-chip { flex-shrink: 0; padding: 7px 13px; border: 1px solid var(--line); border-radius: 99px; background: #fff; color: #5b5bd6; font: 600 11.5px 'Poppins', sans-serif; cursor: pointer; transition: transform .2s, background .2s, border-color .2s; }
    .nr-chip:hover { background: #f5f3ff; border-color: #c4b5fd; transform: translateY(-2px); }
    .nr-in { flex: 0 0 auto; padding: 12px 22px 14px; background: #fff; }
    .nr-chips:not(.show) + .nr-in { border-top: 1px solid var(--line); }
    .nr-box { display: flex; align-items: flex-end; gap: 8px; max-width: 820px; margin: 0 auto; padding: 7px; border: 1.5px solid var(--line); border-radius: 20px; background: #fff; transition: border-color .2s, box-shadow .2s; }
    .nr-box:focus-within { border-color: #a5b4fc; box-shadow: 0 0 0 4px rgba(79,70,229,.1); }
    .nr-ta { flex: 1; min-width: 0; min-height: 44px; max-height: 120px; padding: 12px 10px; border: 0; outline: 0; resize: none; background: transparent; color: var(--tx); font: 13.5px/1.5 'Poppins', sans-serif; }
    .nr-ta::placeholder { color: #a3a3c2; }
    .nr-bt { position: relative; width: 44px; height: 44px; flex-shrink: 0; display: grid; place-items: center; border: 0; border-radius: 14px; cursor: pointer; transition: transform .2s, opacity .2s; }
    .nr-bt svg.i { width: 20px; height: 20px; }
    .nr-mic { color: var(--mut); background: #f5f3ff; }
    .nr-mic:hover:not(:disabled) { color: var(--pri); }
    .nr-mic.on { color: #fff; background: #ef4444; }
    .nr-mic.on::after { content: ""; position: absolute; inset: 0; border: 2px solid #ef4444; border-radius: inherit; animation: nr-ring 1.2s ease-out infinite; }
    @keyframes nr-ring { to { transform: scale(1.6); opacity: 0; } }
    .nr-send { color: #fff; background: linear-gradient(135deg, var(--ink), var(--pri)); box-shadow: 0 8px 18px rgba(30,42,107,.3); }
    .nr-send:hover:not(:disabled) { transform: translateY(-2px); }
    .nr-send:active:not(:disabled) { transform: scale(.94); }
    .nr-bt:disabled { opacity: .4; cursor: not-allowed; box-shadow: none; }
    .nr-send.fly svg { animation: nr-fly .5s ease; }
    @keyframes nr-fly { 40% { transform: translate(9px, -9px); opacity: 0; } 41% { transform: translate(-9px, 9px); } 100% { transform: none; opacity: 1; } }
    .nr-hint { margin-top: 8px; text-align: center; color: #a3a3c2; font-size: 10.5px; line-height: 1.5; }

    @media (max-width: 800px) {
        .nr { padding: 8px; }
        .nr-shell, .nr-shell.sb-closed { display: block; position: relative; border-radius: 20px; }
        .nr-side { position: absolute; z-index: 6; top: 0; bottom: 0; left: 0; width: 290px; box-shadow: 0 0 40px rgba(10,6,60,.3); transition: transform .3s ease; }
        .nr-shell.sb-closed .nr-side { transform: translateX(-100%); box-shadow: none; }
        .nr-shell:not(.sb-closed) .nr-scrim { display: block; }
        .sd-a { display: flex; }
        .nr-head { padding: 12px 14px; }
        .nr-msgs { padding: 18px 13px; }
        .nr-bw { max-width: 88%; }
        .nr-wel img { width: 108px; height: 108px; }
        .nr-cards { grid-template-columns: 1fr; }
        .nr-chips { padding: 10px 12px 0; }
        .nr-in { padding: 10px 12px 12px; }
    }
    @media (prefers-reduced-motion: reduce) { .nr *, .nr *::before, .nr *::after { animation: none !important; transition: none !important; } }
</style>

<div class="nr">
    <div class="nr-bg" aria-hidden="true"><i class="nr-o1"></i><i class="nr-o2"></i><div class="nr-gridbg"></div></div>

    <div class="nr-shell" id="shell">

        {{-- ===== RIWAYAT ===== --}}
        <aside class="nr-side" id="side" aria-label="Riwayat percakapan">
            <div class="sd-in">
                <div class="sd-top">
                    <a href="{{ route('chat') }}" class="sd-new"><svg class="i" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Percakapan baru</a>
                    <div class="sd-sw">
                        <svg class="i" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/></svg>
                        <input type="search" id="sdq" placeholder="Cari percakapan" aria-label="Cari percakapan">
                    </div>
                </div>
                <div class="sd-list" id="convList">
                    @foreach ($groups as $label => $items)
                        @if (count($items))
                            <div class="sd-grp" data-g="{{ $label }}">
                                <div class="sd-g">{{ $label }}</div>
                                @foreach ($items as $c)
                                    <div class="sd-i {{ $active && $active->id === $c->id ? 'on' : '' }}" data-id="{{ $c->id }}">
                                        <a href="{{ route('chat.show', $c->id) }}" class="sd-t" title="{{ $c->title }}">{{ $c->title }}</a>
                                        <span class="sd-a">
                                            <button type="button" data-act="rename" title="Ubah nama" aria-label="Ubah nama"><svg class="i" viewBox="0 0 24 24">{!! $pen !!}</svg></button>
                                            <button type="button" data-act="del" title="Hapus" aria-label="Hapus"><svg class="i" viewBox="0 0 24 24">{!! $trash !!}</svg></button>
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                    <div class="sd-empty" id="sdEmpty" @if ($conversations->isNotEmpty()) hidden @endif>Belum ada riwayat.<br>Percakapanmu dengan NARA akan tersimpan di sini.</div>
                </div>
            </div>
        </aside>
        <div class="nr-scrim" id="scrim"></div>

        {{-- ===== CHAT ===== --}}
        <div class="nr-main">
            <header class="nr-head">
                <div class="nr-hl">
                    <button type="button" class="nr-ic" id="sbToggle" aria-label="Tampilkan atau sembunyikan riwayat" title="Riwayat percakapan">
                        <svg class="i" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="M9 4v16"/></svg>
                    </button>
                    <div class="nr-av"><img src="{{ asset('images/happy.png') }}" alt="NARA"></div>
                    <div>
                        <div class="nr-nm">NARA</div>
                        <div class="nr-st"><span class="nr-dot"></span>Online · Academic Assistant</div>
                    </div>
                </div>
                <button type="button" class="nr-ic" id="voiceToggle" aria-label="Suara NARA" aria-pressed="true">
                    <svg class="i on" viewBox="0 0 24 24"><path d="M11 5L6 9H3v6h3l5 4V5z"/><path d="M15.5 8.5a5 5 0 0 1 0 7M18.5 5.5a9 9 0 0 1 0 13"/></svg>
                    <svg class="i off" viewBox="0 0 24 24"><path d="M11 5L6 9H3v6h3l5 4V5z"/><path d="M22 9l-6 6M16 9l6 6"/></svg>
                </button>
            </header>

            <div class="nr-msgs" id="messages">
                @if ($messages->isEmpty())
                    <div class="nr-wel" id="welcome">
                        <img src="{{ asset('images/happy.png') }}" alt="NARA" style="--i:0">
                        <h1 style="--i:1">Hai {{ $nama }}, aku <em>NARA</em></h1>
                        <p style="--i:2">Aku siap membantu kamu memahami kondisi akademik, rekomendasi mata kuliah, dan rencana studi berdasarkan data ARAH.</p>
                        <div class="nr-cards" style="--i:3">
                            @foreach ($prompts as $p)
                                <button type="button" class="nr-card" data-prompt="{{ $p[2] }}">
                                    <span class="nr-ci"><svg class="i" viewBox="0 0 24 24">{!! $p[3] !!}</svg></span>
                                    <span>{{ $p[0] }}<small>{{ $p[1] }}</small></span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @else
                    @foreach ($messages as $message)
                        @if ($message->role === 'assistant')
                            <div class="nr-m a">
                                <div class="nr-sa"><img src="{{ asset('images/happy.png') }}" alt="NARA"></div>
                                <div class="nr-bw"><div class="nr-who">NARA</div><div class="nr-b" data-md>{{ $message->message }}</div></div>
                            </div>
                        @else
                            <div class="nr-m u"><div class="nr-bw"><div class="nr-b">{{ $message->message }}</div></div></div>
                        @endif
                    @endforeach
                @endif

                <div class="nr-ty" id="typing" aria-live="polite">
                    <div class="nr-sa"><img src="{{ asset('images/happy.png') }}" alt="NARA"></div>
                    <div class="nr-tb"><div class="nr-dots"><i></i><i></i><i></i></div><span class="nr-tt" id="typingText">NARA sedang berpikir...</span></div>
                </div>
            </div>

            <div class="nr-chips {{ $messages->isEmpty() ? '' : 'show' }}" id="chips">
                @foreach ($prompts as $p)
                    <button type="button" class="nr-chip" data-prompt="{{ $p[2] }}">{{ $p[0] }}</button>
                @endforeach
            </div>

            <div class="nr-in">
                <form id="chatForm">
                    @csrf
                    <div class="nr-box">
                        <textarea id="messageInput" class="nr-ta" rows="1" maxlength="2000" placeholder="Tulis pertanyaanmu untuk NARA..." aria-label="Pertanyaan untuk NARA"></textarea>
                        <button type="button" id="voiceButton" class="nr-bt nr-mic" aria-label="Input suara" title="Input suara">
                            <svg class="i" viewBox="0 0 24 24"><rect x="9" y="3" width="6" height="12" rx="3"/><path d="M5 11a7 7 0 0 0 14 0M12 18v3"/></svg>
                        </button>
                        <button type="submit" id="sendButton" class="nr-bt nr-send" aria-label="Kirim" title="Kirim" disabled>
                            <svg class="i" viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                        </button>
                    </div>
                </form>
                <p class="nr-hint">Enter untuk kirim, Shift+Enter untuk baris baru. NARA dapat melakukan kesalahan, jadi diskusikan keputusan penting dengan Dosen PA.</p>
            </div>
        </div>

    </div>
</div>

<script>
(function () {
    var $ = function (id) { return document.getElementById(id); };
    var form = $('chatForm'), input = $('messageInput'), send = $('sendButton'), mic = $('voiceButton');
    var msgs = $('messages'), typing = $('typing'), typingText = $('typingText'), chips = $('chips');
    var voiceBtn = $('voiceToggle'), shell = $('shell'), list = $('convList'), empty = $('sdEmpty');
    var token = form.querySelector('input[name="_token"]').value;
    var url = @json(route('chat.send'));
    var base = @json(url('/chat'));
    var avatar = @json(asset('images/happy.png'));
    var convId = @json($active?->id);
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var placeholder = input.placeholder, busy = false, finish = null;
    var PEN = @json($pen), TRASH = @json($trash);

    /* ---------- Teks: markdown ringan yang aman (HTML di-escape dulu) ---------- */
    function md(t) {
        t = t.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        t = t.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>').replace(/`([^`\n]+)`/g, '<code>$1</code>');
        t = t.replace(/^#{1,3}\s*(.+)$/gm, '<strong>$1</strong>').replace(/^[*-]\s+(.+)$/gm, '• $1');
        return t;
    }
    document.querySelectorAll('.nr-b[data-md]').forEach(function (b) { b.innerHTML = md(b.textContent); });

    /* ---------- Sidebar ---------- */
    if (window.matchMedia('(max-width: 800px)').matches) shell.classList.add('sb-closed');
    $('sbToggle').addEventListener('click', function () { shell.classList.toggle('sb-closed'); });
    $('scrim').addEventListener('click', function () { shell.classList.add('sb-closed'); });

    function tidy() {
        list.querySelectorAll('.sd-grp').forEach(function (g) { if (!g.querySelector('.sd-i')) g.remove(); });
        empty.hidden = !!list.querySelector('.sd-i');
    }
    function api(method, id, body) {
        return fetch(base + '/' + id, { method: method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token }, body: body ? JSON.stringify(body) : undefined });
    }
    function btn(act, title, svg) {
        var b = document.createElement('button');
        b.type = 'button'; b.dataset.act = act; b.title = title; b.setAttribute('aria-label', title);
        b.innerHTML = '<svg class="i" viewBox="0 0 24 24">' + svg + '</svg>';
        return b;
    }
    /* Tambah / pindahkan percakapan ke paling atas grup "Hari ini" */
    function upsert(c) {
        var item = list.querySelector('.sd-i[data-id="' + c.id + '"]');
        if (!item) {
            item = document.createElement('div'); item.className = 'sd-i'; item.dataset.id = c.id;
            var a = document.createElement('a'); a.className = 'sd-t'; a.href = base + '/' + c.id; a.textContent = c.title; a.title = c.title;
            var act = document.createElement('span'); act.className = 'sd-a';
            act.appendChild(btn('rename', 'Ubah nama', PEN)); act.appendChild(btn('del', 'Hapus', TRASH));
            item.appendChild(a); item.appendChild(act);
        }
        var g = list.querySelector('.sd-grp[data-g="Hari ini"]');
        if (!g) {
            g = document.createElement('div'); g.className = 'sd-grp'; g.dataset.g = 'Hari ini';
            var h = document.createElement('div'); h.className = 'sd-g'; h.textContent = 'Hari ini';
            g.appendChild(h); list.insertBefore(g, list.firstChild);
        }
        g.insertBefore(item, g.querySelector('.sd-g').nextSibling);
        list.querySelectorAll('.sd-i.on').forEach(function (x) { x.classList.remove('on'); });
        item.classList.add('on');
        tidy();
    }
    list.addEventListener('click', async function (e) {
        var b = e.target.closest('[data-act]'); if (!b) return;
        var item = b.closest('.sd-i'), id = item.dataset.id, t = item.querySelector('.sd-t');
        if (b.dataset.act === 'rename') {
            var n = prompt('Ubah nama percakapan', t.textContent);
            if (!n || !n.trim()) return;
            var r = await api('PATCH', id, { title: n.trim() });
            if (r.ok) { t.textContent = n.trim().slice(0, 60); t.title = t.textContent; }
        } else if (confirm('Hapus percakapan ini? Tindakan ini tidak bisa dibatalkan.')) {
            var d = await api('DELETE', id);
            if (!d.ok) return;
            item.remove(); tidy();
            if (String(id) === String(convId)) location.href = @json(route('chat'));
        }
    });
    $('sdq').addEventListener('input', function () {
        var q = this.value.trim().toLowerCase();
        list.querySelectorAll('.sd-i').forEach(function (i) { i.style.display = i.textContent.toLowerCase().indexOf(q) > -1 ? '' : 'none'; });
        list.querySelectorAll('.sd-grp').forEach(function (g) { g.style.display = [].some.call(g.querySelectorAll('.sd-i'), function (i) { return i.style.display !== 'none'; }) ? '' : 'none'; });
    });

    /* ---------- Suara NARA ---------- */
    var voiceOn = true;
    try { voiceOn = localStorage.getItem('naraVoice') !== 'off'; } catch (e) {}
    function paintVoice() {
        voiceBtn.classList.toggle('muted', !voiceOn);
        voiceBtn.setAttribute('aria-pressed', String(voiceOn));
        voiceBtn.title = voiceOn ? 'Suara NARA: aktif' : 'Suara NARA: mati';
    }
    voiceBtn.addEventListener('click', function () {
        voiceOn = !voiceOn;
        try { localStorage.setItem('naraVoice', voiceOn ? 'on' : 'off'); } catch (e) {}
        if (!voiceOn && window.speechSynthesis) window.speechSynthesis.cancel();
        paintVoice();
    });
    function speak(text) {
        if (!voiceOn || !window.speechSynthesis || !text) return;
        var u = new SpeechSynthesisUtterance(text.replace(/[*#`]/g, ''));
        u.lang = 'id-ID';
        window.speechSynthesis.cancel();
        window.speechSynthesis.speak(u);
    }

    /* ---------- Util chat ---------- */
    function el(tag, cls, text) { var e = document.createElement(tag); if (cls) e.className = cls; if (text) e.textContent = text; return e; }
    function bottom(smooth) { msgs.scrollTo({ top: msgs.scrollHeight, behavior: smooth && !reduce ? 'smooth' : 'auto' }); }
    function resize() { input.style.height = 'auto'; input.style.height = Math.min(input.scrollHeight, 120) + 'px'; }
    var SR = window.SpeechRecognition || window.webkitSpeechRecognition;
    function sync() { send.disabled = busy || !input.value.trim(); mic.disabled = busy || !SR; }

    function add(role, text, isError) {
        var m = el('div', 'nr-m ' + (role === 'assistant' ? 'a' : 'u') + ' new');
        if (role === 'assistant') {
            var a = el('div', 'nr-sa'), im = el('img');
            im.src = avatar; im.alt = 'NARA'; a.appendChild(im); m.appendChild(a);
        }
        var w = el('div', 'nr-bw');
        if (role === 'assistant') w.appendChild(el('div', 'nr-who', 'NARA'));
        var b = el('div', 'nr-b' + (isError ? ' err' : ''), text);
        w.appendChild(b); m.appendChild(w);
        msgs.insertBefore(m, typing);
        bottom(true);
        return b;
    }

    /* Balasan NARA muncul bertahap, lalu diformat */
    function reveal(b, text) {
        if (reduce || text.length < 2) { b.innerHTML = md(text); bottom(true); return; }
        var n = 0, step = Math.max(2, Math.ceil(text.length / 110));
        b.classList.add('caret');
        function done() { clearInterval(t); b.innerHTML = md(text); b.classList.remove('caret'); bottom(); finish = null; }
        var t = setInterval(function () {
            n += step;
            if (n >= text.length) { done(); } else { b.innerHTML = md(text.slice(0, n)); bottom(); }
        }, 20);
        finish = done;
    }

    var phrases = ['NARA sedang berpikir...', 'Membaca data akademikmu...', 'Menyusun jawaban...'], pi = 0, pt = null;
    function typingOn() {
        pi = 0; typingText.textContent = phrases[0]; typing.classList.add('on');
        pt = setInterval(function () {
            typingText.style.opacity = 0;
            setTimeout(function () { pi = (pi + 1) % phrases.length; typingText.textContent = phrases[pi]; typingText.style.opacity = 1; }, 220);
        }, 1800);
        bottom(true);
    }
    function typingOff() { typing.classList.remove('on'); clearInterval(pt); typingText.style.opacity = 1; }

    /* ---------- Kirim ---------- */
    async function submit(text) {
        text = (text || input.value).trim();
        if (!text || busy) return;
        if (finish) finish();
        var welcome = $('welcome');
        if (welcome) welcome.remove();
        chips.classList.add('show');

        add('user', text);
        input.value = ''; resize();
        busy = true; sync();
        send.classList.remove('fly'); void send.offsetWidth; send.classList.add('fly');
        typingOn();

        try {
            var res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify({ message: text, conversation_id: convId })
            });
            var data = await res.json();
            typingOff();
            if (!res.ok) throw new Error(data.message || 'Terjadi kesalahan.');
            if (!data.reply) throw new Error('Balasan kosong.');
            if (data.conversation) {
                convId = data.conversation.id;
                upsert(data.conversation);
                if (data.conversation.is_new) history.replaceState(null, '', base + '/' + convId);
            }
            reveal(add('assistant', ''), data.reply);
            speak(data.reply);
        } catch (err) {
            typingOff();
            reveal(add('assistant', '', true), 'Maaf, terjadi kesalahan saat menghubungi NARA.');
            console.error('NARA ERROR:', err);
        } finally {
            busy = false; sync(); input.focus();
        }
    }

    form.addEventListener('submit', function (e) { e.preventDefault(); submit(); });
    input.addEventListener('input', function () { resize(); sync(); });
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) { e.preventDefault(); form.requestSubmit(); }
    });
    document.querySelectorAll('[data-prompt]').forEach(function (b) {
        b.addEventListener('click', function () { submit(b.dataset.prompt); });
    });

    /* ---------- Input suara ---------- */
    if (SR) {
        var rec = new SR(), listening = false;
        rec.lang = 'id-ID'; rec.interimResults = true; rec.continuous = false;
        mic.addEventListener('click', function () {
            if (listening) { rec.stop(); return; }
            try {
                if (window.speechSynthesis) window.speechSynthesis.cancel();
                rec.start(); listening = true;
                mic.classList.add('on'); input.placeholder = 'Mendengarkan...';
            } catch (e) {}
        });
        rec.onresult = function (e) {
            var t = '';
            for (var i = e.resultIndex; i < e.results.length; i++) t += e.results[i][0].transcript;
            input.value = t; resize(); sync();
        };
        rec.onend = rec.onerror = function () {
            listening = false; mic.classList.remove('on'); input.placeholder = placeholder; input.focus();
        };
    } else {
        mic.title = 'Browser ini belum mendukung input suara';
    }

    paintVoice(); sync(); tidy(); bottom(false);
})();
</script>

@endsection