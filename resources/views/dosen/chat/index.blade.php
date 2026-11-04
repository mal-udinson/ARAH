@extends('layouts.app')

@section('title', 'Chat Mahasiswa — ARAH')

@section('content')

@php
    $ic = [
        'chat' => '<path d="M21 11.5a8.4 8.4 0 0 1-8.7 8.4 9 9 0 0 1-4.2-1L3 20l1.2-5.1A8.4 8.4 0 1 1 21 11.5z"/>',
        'go'   => '<path d="M5 12h14M13 6l6 6-6 6"/>',
    ];
@endphp

<style>
    .aa { --ink:#1E2A6B; --pri:#4F46E5; --tx:#334155; --mut:#64748b; --line:#e8e4ff; --soft:#f6f4ff;
        position: relative; overflow: hidden; min-height: calc(100vh - 70px); padding: 30px 22px 60px; color: var(--tx);
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; background: linear-gradient(165deg, #15174a 0%, #2f2a9b 50%, #8b2fc9 100%); }
    .aa *, .aa *::before, .aa *::after { box-sizing: border-box; }
    .aa-wrap { position: relative; z-index: 2; width: min(900px, 100%); margin: 0 auto; }
    .aa .i { width: 20px; height: 20px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }

    .aa-bg { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
    .aa-orb { position: absolute; border-radius: 50%; filter: blur(80px); animation: aa-dr 24s ease-in-out infinite alternate; }
    .aa-o1 { width: 500px; height: 500px; top: -170px; left: -140px; background: #7C3AED; opacity: .5; }
    .aa-o2 { width: 440px; height: 440px; bottom: -160px; right: -120px; background: #d946ef; opacity: .4; animation-direction: alternate-reverse; }
    @keyframes aa-dr { to { transform: translate(120px, 90px) scale(1.15); } }
    .aa-grid { position: absolute; inset: 0; background-size: 56px 56px; animation: aa-gm 40s linear infinite;
        background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
        -webkit-mask-image: linear-gradient(#000 20%, transparent 95%); mask-image: linear-gradient(#000 20%, transparent 95%); }
    @keyframes aa-gm { to { background-position: 0 56px, 56px 0; } }

    .aa-hero { margin-bottom: 24px; padding: 14px 8px 8px; color: #fff; }
    .aa-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .aa-chip { display: inline-flex; align-items: center; gap: 7px; padding: 6px 12px; border-radius: 99px; background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.26); font-size: 11.5px; font-weight: 600; }
    .aa-chip .i { width: 14px; height: 14px; }
    .aa-hero h1 { margin: 0 0 8px; font-size: clamp(26px, 4vw, 34px); line-height: 1.12; font-weight: 700; letter-spacing: -.035em; }
    .aa-hero p { margin: 0; max-width: 520px; color: rgba(255,255,255,.85); font-size: 13.5px; line-height: 1.7; }

    .aa-card { background: #fff; border: 1px solid rgba(255,255,255,.7); border-radius: 22px; box-shadow: 0 18px 46px rgba(8,16,70,.3); overflow: hidden; animation: aa-in .6s ease both; }
    @keyframes aa-in { from { opacity: 0; transform: translateY(16px); } }

    .aa-act { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 18px 22px; border-bottom: 1px solid var(--line); text-decoration: none; color: inherit; transition: background .2s, transform .2s; }
    .aa-card .aa-act:last-child { border-bottom: 0; }
    .aa-act:hover { background: #faf9ff; transform: translateX(4px); }
    .aa-act:focus-visible { outline: 3px solid #a5b4fc; outline-offset: -2px; }

    .aa-av { width: 42px; height: 42px; display: grid; place-items: center; flex-shrink: 0; border-radius: 13px; background: linear-gradient(135deg, #4F46E5, #7C3AED); color: #fff; font-size: 15px; font-weight: 700; }
    .aa-nm { display: flex; align-items: center; gap: 14px; min-width: 0; }
    .aa-nm b { display: block; color: var(--ink); font-size: 13.5px; font-weight: 700; overflow-wrap: anywhere; }
    .aa-nm small { display: block; margin-top: 3px; color: var(--mut); font-size: 11px; }

    .aa-go { display: inline-flex; align-items: center; gap: 6px; flex-shrink: 0; color: var(--pri); font-size: 11.5px; font-weight: 600; }
    .aa-go .i { width: 15px; height: 15px; transition: transform .2s; }
    .aa-act:hover .aa-go .i { transform: translateX(3px); }

    .aa-nil { padding: 40px 20px; text-align: center; color: #94a3b8; font-size: 13px; }

    @media (max-width: 460px) {
        .aa { padding: 16px 12px 36px; }
        .aa-act { padding: 16px; }
        .aa-nm small { display: none; }
    }
    @media (prefers-reduced-motion: reduce) { .aa *, .aa *::before, .aa *::after { animation: none !important; transition: none !important; } }
</style>

<div class="aa">

    <div class="aa-bg" aria-hidden="true">
        <div class="aa-grid"></div><i class="aa-orb aa-o1"></i><i class="aa-orb aa-o2"></i>
    </div>

    <div class="aa-wrap">

        <section class="aa-hero">
            <div class="aa-chips">
                <span class="aa-chip"><svg class="i" viewBox="0 0 24 24">{!! $ic['chat'] !!}</svg>{{ number_format($mahasiswa->count()) }} mahasiswa bimbingan</span>
            </div>
            <h1>Chat Mahasiswa</h1>
            <p>Pilih mahasiswa bimbingan untuk membuka percakapan.</p>
        </section>

        <div class="aa-card">

            @forelse ($mahasiswa as $item)
                <a class="aa-act" href="{{ route('dosen.chat.show', $item->id) }}">

                    <div class="aa-nm">
                        <span class="aa-av">{{ strtoupper(mb_substr($item->nama, 0, 1)) }}</span>
                        <div>
                            <b>{{ $item->nama }}</b>
                            <small>NIM: {{ $item->nim }}</small>
                        </div>
                    </div>

                    <span class="aa-go">Buka Chat<svg class="i" viewBox="0 0 24 24">{!! $ic['go'] !!}</svg></span>

                </a>
            @empty
                <div class="aa-nil">Belum ada mahasiswa bimbingan.</div>
            @endforelse

        </div>

    </div>
</div>

@endsection