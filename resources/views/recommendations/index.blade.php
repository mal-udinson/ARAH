@extends('layouts.app')

@section('title', 'Rekomendasi — ARAH')

@section('content')

@php
    $nama     = explode(' ', trim($m->nama))[0];
    $daftar   = $items->take(5)->values();
    $ada      = $daftar->count() > 0;
    $punyaAse = $m->asesmen->count() > 0;

    $rataMinat = $m->asesmen->avg('skor');
    $rataMinat = $rataMinat !== null ? (int) round($rataMinat) : 0;
    $rataKemam = $m->nilai->avg('nilai_angka');
    $rataKemam = $rataKemam !== null ? (int) round($rataKemam) : 0;
    $topMinat  = $m->asesmen->sortByDesc('skor')->first();

    $dasar = [
        ['Profil minat', 'Bobot 40%', 'Diambil dari 20 pernyataan di asesmen minat, dikelompokkan ke lima bidang.', '<path d="M21 12a9 9 0 1 1-9-9v9z"/>'],
        ['Kemampuan akademik', 'Bobot 60%', 'Diambil dari rata-rata nilai di data akademikmu.', '<path d="M12 3l9 5-9 5-9-5 9-5z"/><path d="M7 11v5c0 1.5 2.2 3 5 3s5-1.5 5-3v-5"/>'],
        ['Skor dan alasan', 'Hasil', 'Setiap mata kuliah diberi skor kecocokan beserta alasan yang bisa kamu baca.', '<path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z"/>'],
    ];
@endphp

<style>
    @property --p { syntax: '<number>'; inherits: false; initial-value: 0; }

    .rc { --ink:#1E2A6B; --pri:#4F46E5; --vio:#7C3AED; --tx:#334155; --mut:#64748b; --line:#e5e3ff;
        position: relative; overflow: hidden; min-height: calc(100vh - 64px); padding: 30px 22px 56px; color: #fff;
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; background: linear-gradient(165deg, #1b1760 0%, #3a2ea6 50%, #6b35e0 100%); }
    .rc *, .rc *::before, .rc *::after { box-sizing: border-box; }
    :where(.rc) h1, :where(.rc) h2, :where(.rc) h3, :where(.rc) p { margin: 0; }
    .rc-bg { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
    .rc-o1, .rc-o2 { position: absolute; border-radius: 50%; filter: blur(80px); animation: rc-dr 28s ease-in-out infinite alternate; }
    .rc-o1 { width: 460px; height: 460px; top: -150px; left: -120px; background: #8b5cf6; opacity: .5; }
    .rc-o2 { width: 400px; height: 400px; bottom: -140px; right: -120px; background: #c026d3; opacity: .26; animation-duration: 32s; }
    @keyframes rc-dr { to { transform: translate(100px, 70px) scale(1.1); } }
    .rc-gridbg { position: absolute; inset: 0; background-size: 56px 56px; -webkit-mask-image: linear-gradient(#000 20%, transparent 95%); mask-image: linear-gradient(#000 20%, transparent 95%);
        background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px); }
    .rc-wrap { position: relative; z-index: 1; width: min(1100px, 100%); margin: 0 auto; }
    .rc-fi { animation: rc-fi .5s ease calc(var(--i, 0) * 80ms) backwards; }
    @keyframes rc-fi { from { opacity: 0; transform: translateY(14px); } }
    @keyframes rc-grow { from { width: 0; } }
    @keyframes rc-bob { 0%, 100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-8px) rotate(2deg); } }

    .rc-flash { margin-bottom: 18px; padding: 13px 16px; border-left: 4px solid; border-radius: 14px; background: #fff; font-size: 12.5px; line-height: 1.6; }
    .rc-flash.ok { color: #15803d; border-color: #10b981; }
    .rc-flash.er { color: #b91c1c; border-color: #ef4444; }

    /* Hero transparan */
    .rc-hero { position: relative; display: flex; align-items: center; justify-content: space-between; gap: 24px; margin-bottom: 26px; padding: 6px 4px 0; }
    .rc-hero h1 { font-size: clamp(28px, 4.4vw, 44px); line-height: 1.15; font-weight: 700; letter-spacing: -.03em; overflow-wrap: anywhere; }
    .rc-hero p { max-width: 540px; margin-top: 8px; color: rgba(255,255,255,.82); font-size: 14px; line-height: 1.75; }
    .rc-hm { flex-shrink: 0; width: 230px; height: 170px; display: grid; place-items: center; }
    .rc-hm img { width: 210px; height: 210px; object-fit: contain; filter: drop-shadow(0 18px 28px rgba(10,6,60,.45)) drop-shadow(0 0 28px rgba(196,181,253,.35)); animation: rc-bob 4s ease-in-out infinite; }

    /* Ringkasan */
    .rc-sum { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 18px; margin-bottom: 30px; padding-top: 22px; border-top: 1px solid rgba(255,255,255,.2); }
    .rc-sum div { padding-left: 16px; border-left: 1px solid rgba(255,255,255,.2); }
    .rc-sum div:first-child { padding-left: 0; border-left: 0; }
    .rc-sum small { display: block; color: rgba(255,255,255,.7); font-size: 12px; }
    .rc-sum b { display: block; margin-top: 4px; font-size: 18px; font-weight: 700; line-height: 1.3; overflow-wrap: anywhere; }

    .rc-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 15px; margin-bottom: 16px; }
    .rc-head h2 { font-size: 17px; font-weight: 700; letter-spacing: -.01em; }
    .rc-head p { margin-top: 4px; color: rgba(255,255,255,.72); font-size: 12px; line-height: 1.6; }
    .rc-pill { flex-shrink: 0; padding: 6px 12px; border-radius: 99px; background: rgba(255,255,255,.16); font-size: 11.5px; font-weight: 600; }

    /* Kartu rekomendasi */
    .rc-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
    .rc-card { display: flex; flex-direction: column; padding: 22px; color: var(--tx); background: #fff; border-radius: 22px; box-shadow: 0 18px 46px rgba(10,6,60,.28); transition: transform .25s, box-shadow .25s; }
    .rc-card:hover { transform: translateY(-3px); box-shadow: 0 26px 56px rgba(10,6,60,.36); }
    .rc-card.top { grid-column: 1 / -1; display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1.1fr); gap: 26px; box-shadow: 0 0 0 2px #c4b5fd, 0 22px 50px rgba(10,6,60,.35); background: linear-gradient(180deg, #faf8ff, #fff 60%); }
    .rc-l { flex: 1; }
    .rc-rk { display: flex; align-items: center; justify-content: space-between; }
    .rc-no { padding: 5px 11px; border-radius: 99px; color: var(--pri); background: #eef2ff; font-size: 11px; font-weight: 700; }
    .rc-card.top .rc-no { color: #fff; background: linear-gradient(135deg, var(--pri), var(--vio)); }
    .rc-ring { --p: var(--v); position: relative; width: 54px; height: 54px; display: grid; place-items: center; border-radius: 50%; background: conic-gradient(var(--pri) calc(var(--p) * 1%), #eceaff 0); animation: rc-ring 1.3s cubic-bezier(.2,.8,.2,1) .3s backwards; }
    @keyframes rc-ring { from { --p: 0; } }
    .rc-ring::before { content: ""; position: absolute; inset: 6px; border-radius: 50%; background: #fff; }
    .rc-ring b { position: relative; color: var(--ink); font-size: 12px; }
    .rc-cc { margin-top: 14px; color: #94a3b8; font-size: 10.5px; font-weight: 600; }
    .rc-cn { margin-top: 3px; color: var(--ink); font-size: 17px; line-height: 1.4; font-weight: 700; letter-spacing: -.01em; }
    .rc-card.top .rc-cn { font-size: 22px; }
    .rc-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
    .rc-tags span { padding: 4px 9px; border-radius: 8px; color: #5b5bd6; background: #f8f7ff; border: 1px solid var(--line); font-size: 10.5px; font-weight: 600; }
    .rc-why { margin-top: 16px; padding-top: 14px; border-top: 1px dashed var(--line); }
    .rc-card.top .rc-why { margin-top: 0; padding: 0 0 0 26px; border-top: 0; border-left: 1px dashed var(--line); }
    .rc-wh { color: var(--ink); font-size: 13px; font-weight: 700; }
    .rc-why p { margin-top: 6px; color: var(--mut); font-size: 12.5px; line-height: 1.8; }
    .rc-mi { display: flex; justify-content: space-between; gap: 10px; margin-top: 14px; font-size: 12px; font-weight: 600; }
    .rc-mi span:last-child { color: var(--pri); }
    .rc-tr { height: 6px; margin-top: 6px; overflow: hidden; border-radius: 99px; background: #eceaff; }
    .rc-tr i { display: block; width: var(--w); height: 100%; border-radius: inherit; background: linear-gradient(90deg, var(--pri), var(--vio)); animation: rc-grow 1s cubic-bezier(.2,.8,.2,1) .3s backwards; }

    /* Kosong */
    .rc-empty { padding: 36px 24px; text-align: center; color: var(--mut); background: #fff; border-radius: 22px; box-shadow: 0 18px 46px rgba(10,6,60,.28); font-size: 13px; line-height: 1.8; }
    .rc-empty svg { width: 96px; height: 96px; margin-bottom: 10px; animation: rc-spin 40s linear infinite; }
    .rc-empty b { display: block; color: var(--ink); font-size: 17px; }
    @keyframes rc-spin { to { transform: rotate(360deg); } }
    .rc-btns { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; margin-top: 18px; }
    .rc-btn { display: inline-flex; align-items: center; justify-content: center; min-height: 44px; padding: 0 20px; border-radius: 13px; font-size: 12.5px; font-weight: 600; text-decoration: none; transition: transform .2s; }
    .rc-btn:hover { transform: translateY(-2px); }
    .rc-btn:focus-visible { outline: 3px solid #c4b5fd; outline-offset: 3px; }
    .rc-btn.p { color: #fff; background: linear-gradient(135deg, var(--ink), var(--pri)); box-shadow: 0 10px 24px rgba(30,42,107,.3); }
    .rc-btn.s { color: var(--pri); background: #eef2ff; border: 1px solid #e0e7ff; }

    .rc-sec { margin-top: 30px; }
    .rc-sg { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
    .rc-st { padding: 20px; color: var(--tx); background: #fff; border-radius: 20px; box-shadow: 0 18px 46px rgba(10,6,60,.28); }
    .rc-si { width: 44px; height: 44px; display: grid; place-items: center; border-radius: 14px; color: var(--pri); background: linear-gradient(135deg, #eef2ff, #f3e8ff); }
    .rc-si svg { width: 22px; height: 22px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
    .rc-st b { display: block; margin-top: 14px; color: var(--ink); font-size: 14px; }
    .rc-st small { display: inline-block; margin-top: 6px; padding: 2px 9px; border-radius: 99px; background: #f5f3ff; color: #5b5bd6; font-size: 10.5px; font-weight: 600; }
    .rc-st p { margin-top: 8px; color: var(--mut); font-size: 12px; line-height: 1.7; }

    .rc-note { display: flex; gap: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,.2); color: rgba(255,255,255,.7); font-size: 12px; line-height: 1.7; }
    .rc-note b { color: #fff; }

    @media (max-width: 900px) {
        .rc-card.top { grid-template-columns: 1fr; gap: 0; }
        .rc-card.top .rc-why { margin-top: 16px; padding: 14px 0 0; border-left: 0; border-top: 1px dashed var(--line); }
        .rc-sum { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .rc-sum div:nth-child(odd) { padding-left: 0; border-left: 0; }
        .rc-sg { grid-template-columns: 1fr; }
    }
    @media (max-width: 700px) {
        .rc { padding: 20px 12px 40px; }
        .rc-hero { display: block; }
        .rc-hero > div:first-child { padding-right: 100px; }
        .rc-hm { position: absolute; top: 0; right: 0; width: 104px; height: 104px; }
        .rc-hm img { width: 96px; height: 96px; }
        .rc-grid { grid-template-columns: 1fr; }
        .rc-card { padding: 20px 16px; }
        .rc-head { flex-direction: column; align-items: flex-start; }
        .rc-btns, .rc-btn { width: 100%; }
    }
    @media (prefers-reduced-motion: reduce) { .rc *, .rc *::before, .rc *::after { animation: none !important; transition: none !important; } }
</style>

<div class="rc">
    <div class="rc-bg" aria-hidden="true"><i class="rc-o1"></i><i class="rc-o2"></i><div class="rc-gridbg"></div></div>

    <div class="rc-wrap">

        @if (session('success'))<div class="rc-flash ok">{{ session('success') }}</div>@endif
        @if (session('error'))<div class="rc-flash er">{{ session('error') }}</div>@endif

        <section class="rc-hero rc-fi">
            <div>
                <h1>Rekomendasi studi {{ $nama }}</h1>
                <p>NARA menganalisis profil minat dan data akademikmu untuk membantu menemukan mata kuliah yang paling relevan sebagai bahan pertimbangan studi.</p>
            </div>
            <div class="rc-hm"><img src="{{ asset('images/duduk.png') }}" alt="NARA"></div>
        </section>

        @if ($ada)

            <div class="rc-sum rc-fi" style="--i:1">
                <div><small>Rata-rata minat</small><b>{{ $rataMinat }}%</b></div>
                <div><small>Kemampuan akademik</small><b>{{ $rataKemam ? $rataKemam.'%' : 'Belum tersedia' }}</b></div>
                <div><small>Minat dominan</small><b>{{ $topMinat->kategori ?? '—' }}</b></div>
                <div><small>Dasar analisis</small><b>Minat 40% + Kemampuan 60%</b></div>
            </div>

            <div class="rc-head rc-fi" style="--i:2">
                <div>
                    <h2>Top {{ $daftar->count() }} rekomendasi</h2>
                    <p>Setiap hasil dilengkapi alasan agar kamu memahami dasar rekomendasi NARA.</p>
                </div>
                <span class="rc-pill">{{ $daftar->count() }} mata kuliah</span>
            </div>

            <div class="rc-grid">
                @foreach ($daftar as $i => $item)
                    @php
                        $mk   = $item->mataKuliah;
                        $kat  = $mk?->kategori;
                        $mi   = $kat ? $m->asesmen->firstWhere('kategori', $kat) : null;
                        $minat = $mi ? (int) round($mi->skor) : $rataMinat;
                        $sk   = (float) $item->skor;
                    @endphp
                    <article class="rc-card {{ $i === 0 ? 'top' : '' }} rc-fi" style="--i:{{ $i + 3 }}">
                        <div class="rc-l">
                            <div class="rc-rk">
                                <span class="rc-no">{{ $i === 0 ? 'Paling cocok' : '#'.($i + 1) }}</span>
                                <div class="rc-ring" style="--v:{{ min(100, max(0, $sk)) }}"><b>{{ round($sk) }}%</b></div>
                            </div>
                            <div class="rc-cc">{{ $mk?->kode ?? '—' }}</div>
                            <h3 class="rc-cn">{{ $mk?->nama ?? 'Mata kuliah' }}</h3>
                            <div class="rc-tags">
                                @if ($kat)<span>{{ $kat }}</span>@endif
                                @if ($mk?->sks)<span>{{ $mk->sks }} SKS</span>@endif
                                @if ($item->status)<span>{{ ucfirst($item->status) }}</span>@endif
                            </div>
                        </div>
                        <div class="rc-why">
                            <div class="rc-wh">Kenapa NARA memilih ini</div>
                            <p>{{ $item->alasan ?: 'NARA merekomendasikan mata kuliah ini berdasarkan kesesuaian profil minat dan kemampuan akademik yang tersedia di ARAH.' }}</p>
                            <div class="rc-mi"><span>{{ $mi ? 'Minat pada '.$kat : 'Rata-rata minat' }}</span><span>{{ $minat }}%</span></div>
                            <div class="rc-tr"><i style="--w:{{ min(100, max(0, $minat)) }}%"></i></div>
                        </div>
                    </article>
                @endforeach
            </div>

        @else

            <section class="rc-empty rc-fi" style="--i:1">
                <svg viewBox="0 0 120 120" fill="none" stroke="#c4b5fd" stroke-width="1.5" aria-hidden="true"><polygon points="60,10 105,35 105,85 60,110 15,85 15,35"/><polygon points="60,30 87,45 87,75 60,90 33,75 33,45" stroke="#a78bfa"/><circle cx="60" cy="60" r="4" fill="#7C3AED" stroke="none"/></svg>
                <b>Belum ada rekomendasi</b>
                @if ($punyaAse)
                    Profil minatmu sudah siap. Jalankan analisis AI supaya NARA menyusun mata kuliah yang cocok beserta alasannya.
                    <div class="rc-btns">
                        <a href="{{ route('recommendations.generate') }}" class="rc-btn p">Jalankan analisis AI</a>
                        <a href="{{ route('interest.profile') }}" class="rc-btn s">Lihat profil minat</a>
                    </div>
                @else
                    Isi asesmen minat dulu, lalu NARA menyusun mata kuliah yang cocok beserta alasannya.
                    <div class="rc-btns"><a href="{{ route('assessment.show') }}" class="rc-btn p">Isi asesmen minat</a></div>
                @endif
            </section>

            <section class="rc-sec rc-fi" style="--i:2">
                <div class="rc-head"><div><h2>Cara NARA menyusun rekomendasi</h2><p>Dua data utama digabung, lalu setiap mata kuliah diberi skor dan alasan.</p></div></div>
                <div class="rc-sg">
                    @foreach ($dasar as $d)
                        <div class="rc-st">
                            <span class="rc-si"><svg viewBox="0 0 24 24" aria-hidden="true">{!! $d[3] !!}</svg></span>
                            <b>{{ $d[0] }}</b>
                            <small>{{ $d[1] }}</small>
                            <p>{{ $d[2] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

        @endif

        <div class="rc-note">
            <span aria-hidden="true">✦</span>
            <div>
                <b>Tentang rekomendasi ARAH</b><br>
                Rekomendasi digunakan sebagai bahan pertimbangan dalam menentukan rencana studi. Hasil AI tidak menggantikan pertimbangan mahasiswa, kurikulum, prasyarat mata kuliah, maupun konsultasi dengan Dosen PA.
            </div>
        </div>

    </div>
</div>

@endsection