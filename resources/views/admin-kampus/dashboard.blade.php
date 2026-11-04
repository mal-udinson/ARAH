@extends('layouts.app')

@section('title', 'Dashboard Admin Kampus - ARAH')

@section('content')

@php
    $rasio = $totalDosen > 0 ? $totalMahasiswa / $totalDosen : null;
    $ic = [
        'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.5a3.5 3.5 0 0 1 0 7M18 14a6.5 6.5 0 0 1 3.5 6"/>',
        'cap'   => '<path d="M12 4L2 9l10 5 10-5-10-5z"/><path d="M6 11v5c0 1.5 3 3 6 3s6-1.5 6-3v-5"/>',
        'id'    => '<rect x="3" y="5" width="18" height="14" rx="3"/><circle cx="9" cy="11" r="2"/><path d="M6 16c.6-1.5 1.8-2 3-2s2.4.5 3 2M15 10h3M15 14h3"/>',
        'bars'  => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
        'build' => '<path d="M4 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16M16 9h2a2 2 0 0 1 2 2v10M2 21h20M8 7h4M8 11h4M8 15h4"/>',
        'tag'   => '<path d="M3 12V4h8l10 10-8 8z"/><circle cx="7.5" cy="8.5" r="1.2"/>',
        'mail'  => '<rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 8l9 6 9-6"/>',
        'pin'   => '<path d="M12 21s7-6.2 7-11a7 7 0 0 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>',
        'plus'  => '<path d="M12 5v14M5 12h14"/>',
        'go'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
    ];
    $stats = [
        ['Mahasiswa', 'users', $totalMahasiswa, 'Terdaftar di kampus ini', 0],
        ['Dosen', 'cap', $totalDosen, 'Dosen pembimbing akademik', 0],
        ['Total akun', 'id', $totalUser, 'Seluruh akun pengguna', 0],
        ['Mahasiswa per dosen', 'bars', $rasio, $rasio === null ? 'Belum ada dosen' : 'Rata-rata beban bimbingan', 1],
    ];
    $info = [
        ['Nama kampus', 'build', $kampus->nama],
        ['Kode kampus', 'tag', $kampus->kode],
        ['Email', 'mail', $kampus->email],
        ['Alamat', 'pin', $kampus->alamat],
    ];
    $aksi = [
        ['Kelola mahasiswa', 'Lihat, ubah, dan hapus data mahasiswa', 'users', route('admin-kampus.mahasiswa.index')],
        ['Tambah mahasiswa', 'Buat akun mahasiswa baru', 'plus', route('admin-kampus.mahasiswa.create')],
        ['Kelola dosen', 'Atur data Dosen PA', 'cap', route('admin-kampus.dosen.index')],
        ['Tambah dosen', 'Buat akun dosen baru', 'plus', route('admin-kampus.dosen.create')],
    ];
@endphp

<style>
    .ak { --ink:#1E2A6B; --pri:#4F46E5; --vio:#9333ea; --tx:#334155; --mut:#64748b; --line:#e8e4ff; --soft:#f6f4ff;
        position: relative; overflow: hidden; min-height: calc(100vh - 70px); padding: 30px 22px 60px; color: var(--tx);
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; background: linear-gradient(165deg, #15174a 0%, #2f2a9b 50%, #8b2fc9 100%); }
    .ak *, .ak *::before, .ak *::after { box-sizing: border-box; }
    .ak-wrap { position: relative; z-index: 2; width: min(1280px, 100%); margin: 0 auto; }
    .ak .i { width: 20px; height: 20px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }

    .ak-bg { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
    .ak-orb { position: absolute; border-radius: 50%; filter: blur(80px); animation: ak-dr 24s ease-in-out infinite alternate; }
    .ak-o1 { width: 500px; height: 500px; top: -170px; left: -140px; background: #7C3AED; opacity: .5; }
    .ak-o2 { width: 440px; height: 440px; bottom: -160px; right: -120px; background: #d946ef; opacity: .4; animation-direction: alternate-reverse; }
    @keyframes ak-dr { to { transform: translate(120px, 90px) scale(1.15); } }
    .ak-grid { position: absolute; inset: 0; background-size: 56px 56px; animation: ak-gm 40s linear infinite;
        background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
        -webkit-mask-image: linear-gradient(#000 20%, transparent 95%); mask-image: linear-gradient(#000 20%, transparent 95%); }
    @keyframes ak-gm { to { background-position: 0 56px, 56px 0; } }
    .ak-arw { position: absolute; bottom: -40px; left: var(--x); width: var(--s); height: var(--s); opacity: 0; animation: ak-up var(--d) linear infinite; animation-delay: var(--dl); }
    .ak-arw::before { content: ""; position: absolute; inset: 0; border-radius: 3px; border-top: 2.5px solid rgba(255,255,255,.6); border-left: 2.5px solid rgba(255,255,255,.6); transform: rotate(45deg) scale(.7); }
    @keyframes ak-up { 0% { transform: translateY(0); opacity: 0; } 12%, 88% { opacity: .45; } 100% { transform: translateY(-115vh); opacity: 0; } }

    .ak-hero { display: grid; grid-template-columns: 1fr auto; align-items: center; gap: 24px; margin-bottom: 26px; padding: 14px 8px 8px; color: #fff; }
    .ak-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .ak-chip { display: inline-flex; align-items: center; gap: 7px; padding: 6px 12px; border-radius: 99px; background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.26); font-size: 11.5px; font-weight: 600; }
    .ak-chip .i { width: 14px; height: 14px; }
    .ak-hero h1 { margin: 0 0 12px; font-size: clamp(28px, 4.4vw, 44px); line-height: 1.12; font-weight: 700; letter-spacing: -.035em; }
    .ak-hero p { margin: 0; max-width: 580px; color: rgba(255,255,255,.85); font-size: 14.5px; line-height: 1.75; }
    .ak-hm { position: relative; width: 230px; height: 200px; display: grid; place-items: center; }
    .ak-hm::before { content: ""; position: absolute; inset: 10px 20px; border-radius: 50%; background: radial-gradient(circle, rgba(255,255,255,.32), transparent 68%); animation: ak-pu 3.2s ease-in-out infinite; }
    .ak-hm img { position: relative; z-index: 1; width: 180px; height: 180px; object-fit: contain; filter: drop-shadow(0 18px 28px rgba(8,16,70,.45)); animation: ak-bob 4s ease-in-out infinite; }
    @keyframes ak-bob { 0%, 100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-10px) rotate(2deg); } }
    @keyframes ak-pu { 50% { transform: scale(1.12); opacity: .6; } }

    .ak-card { background: #fff; border: 1px solid rgba(255,255,255,.7); border-radius: 22px; box-shadow: 0 18px 46px rgba(8,16,70,.3); animation: ak-in .6s ease both; }
    @keyframes ak-in { from { opacity: 0; transform: translateY(16px); } }
    .ak-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-bottom: 20px; }
    .ak-stat { padding: 20px; transition: transform .25s, box-shadow .25s; }
    .ak-stat:hover { transform: translateY(-4px); box-shadow: 0 26px 56px rgba(8,16,70,.38); }
    .ak-ico { width: 46px; height: 46px; display: grid; place-items: center; border-radius: 15px; color: var(--pri); background: linear-gradient(135deg, #eef2ff, #f3e8ff); transition: transform .3s; }
    .ak-stat:hover .ak-ico { transform: rotate(-8deg) scale(1.08); }
    .ak-lb { margin-top: 14px; color: var(--mut); font-size: 12px; font-weight: 500; }
    .ak-val { margin-top: 2px; color: var(--ink); font-size: 32px; line-height: 1.2; font-weight: 700; letter-spacing: -.03em; }
    .ak-nt { margin-top: 2px; color: #94a3b8; font-size: 11px; }

    .ak-cols { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; align-items: stretch; }
    .ak-pn { padding: 24px; }
    .ak-h { margin: 0; color: var(--ink); font-size: 18px; font-weight: 700; letter-spacing: -.01em; }
    .ak-sub { margin: 4px 0 18px; color: var(--mut); font-size: 12.5px; }

    .ak-info { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
    .ak-it { display: flex; gap: 12px; padding: 14px; border: 1px solid var(--line); border-radius: 16px; background: var(--soft); }
    .ak-it .ak-ico { width: 38px; height: 38px; border-radius: 12px; background: #fff; box-shadow: 0 4px 10px rgba(79,70,229,.12); }
    .ak-it .i { width: 18px; height: 18px; }
    .ak-k { color: #94a3b8; font-size: 11px; }
    .ak-v { margin-top: 3px; color: var(--ink); font-size: 13.5px; font-weight: 600; line-height: 1.45; overflow-wrap: anywhere; }
    .ak-v.none { color: #b6bfcd; font-weight: 400; }

    .ak-acts { display: grid; gap: 10px; }
    .ak-act { display: flex; align-items: center; gap: 14px; padding: 13px 14px; border: 1px solid var(--line); border-radius: 16px; background: #fff; color: inherit; text-decoration: none; transition: transform .2s, box-shadow .2s, border-color .2s, background .2s; }
    .ak-act .ak-ico { width: 42px; height: 42px; border-radius: 13px; }
    .ak-act div:nth-child(2) { flex: 1; min-width: 0; }
    .ak-act b { display: block; color: var(--ink); font-size: 13.5px; }
    .ak-act small { color: var(--mut); font-size: 11.5px; }
    .ak-go { color: #a5b4fc; transition: transform .2s, color .2s; }
    .ak-act:hover { transform: translateX(4px); border-color: #c7c2ff; background: #faf9ff; box-shadow: 0 10px 24px rgba(79,70,229,.14); }
    .ak-act:hover .ak-go { color: var(--pri); transform: translateX(3px); }
    .ak-act:focus-visible { outline: 3px solid #a5b4fc; outline-offset: 2px; }

    @media (max-width: 1000px) { .ak-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } .ak-cols { grid-template-columns: 1fr; } }
    @media (max-width: 700px) {
        .ak { padding: 16px 12px 36px; }
        .ak-hero { grid-template-columns: 1fr; padding: 6px 0 0; }
        .ak-hm { position: absolute; top: 4px; right: 0; width: 96px; height: 96px; }
        .ak-hm img { width: 78px; height: 78px; }
        .ak-hero > div:first-child { padding-right: 84px; }
        .ak-info { grid-template-columns: 1fr; }
        .ak-pn { padding: 18px; }
    }
    @media (max-width: 460px) { .ak-stats { gap: 9px; } .ak-stat { padding: 14px; } .ak-val { font-size: 26px; } }
    @media (prefers-reduced-motion: reduce) { .ak *, .ak *::before, .ak *::after { animation: none !important; transition: none !important; } }
</style>

<div class="ak">

    <div class="ak-bg" aria-hidden="true">
        <div class="ak-grid"></div><i class="ak-orb ak-o1"></i><i class="ak-orb ak-o2"></i>
        <i class="ak-arw" style="--x:8%;  --s:22px; --d:18s; --dl:-3s"></i>
        <i class="ak-arw" style="--x:64%; --s:26px; --d:20s; --dl:-6s"></i>
        <i class="ak-arw" style="--x:92%; --s:20px; --d:24s; --dl:-14s"></i>
    </div>

    <div class="ak-wrap">

        {{-- HERO --}}
        <section class="ak-hero">
            <div>
                <div class="ak-chips">
                    <span class="ak-chip"><svg class="i" viewBox="0 0 24 24">{!! $ic['build'] !!}</svg>{{ $kampus->nama }}</span>
                    <span class="ak-chip"><svg class="i" viewBox="0 0 24 24">{!! $ic['tag'] !!}</svg>{{ $kampus->kode }}</span>
                </div>
                <h1>Selamat datang, {{ $admin->name }}</h1>
                <p>Kelola data mahasiswa, dosen, dan akun di {{ $kampus->nama }} dari satu tempat, supaya data akademik tetap akurat dan terkelola.</p>
            </div>
            <div class="ak-hm"><img src="{{ asset('images/happy.png') }}" alt="NARA" onerror="this.style.display='none'"></div>
        </section>

        {{-- STATISTIK --}}
        <div class="ak-stats">
            @foreach ($stats as $s)
                <div class="ak-card ak-stat">
                    <span class="ak-ico"><svg class="i" viewBox="0 0 24 24">{!! $ic[$s[1]] !!}</svg></span>
                    <div class="ak-lb">{{ $s[0] }}</div>
                    @if ($s[2] === null)
                        <div class="ak-val">—</div>
                    @else
                        <div class="ak-val" data-count="{{ $s[2] }}" data-dec="{{ $s[4] }}">{{ number_format($s[2], $s[4]) }}</div>
                    @endif
                    <div class="ak-nt">{{ $s[3] }}</div>
                </div>
            @endforeach
        </div>

        <div class="ak-cols">

            {{-- INFORMASI KAMPUS --}}
            <section class="ak-card ak-pn">
                <h2 class="ak-h">Informasi kampus</h2>
                <p class="ak-sub">Data identitas kampus yang tersimpan di ARAH.</p>
                <div class="ak-info">
                    @foreach ($info as $it)
                        <div class="ak-it">
                            <span class="ak-ico"><svg class="i" viewBox="0 0 24 24">{!! $ic[$it[1]] !!}</svg></span>
                            <div>
                                <div class="ak-k">{{ $it[0] }}</div>
                                <div class="ak-v {{ $it[2] ? '' : 'none' }}">{{ $it[2] ?: 'Belum diisi' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- AKSI CEPAT --}}
            <section class="ak-card ak-pn">
                <h2 class="ak-h">Aksi cepat</h2>
                <p class="ak-sub">Pintasan untuk pekerjaan yang paling sering dilakukan.</p>
                <div class="ak-acts">
                    @foreach ($aksi as $a)
                        <a href="{{ $a[3] }}" class="ak-act">
                            <span class="ak-ico"><svg class="i" viewBox="0 0 24 24">{!! $ic[$a[2]] !!}</svg></span>
                            <div><b>{{ $a[0] }}</b><small>{{ $a[1] }}</small></div>
                            <svg class="i ak-go" viewBox="0 0 24 24">{!! $ic['go'] !!}</svg>
                        </a>
                    @endforeach
                </div>
            </section>

        </div>
    </div>
</div>

<script>
(function () {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    document.querySelectorAll('.ak [data-count]').forEach(function (el) {
        var end = parseFloat(el.dataset.count), dec = +el.dataset.dec || 0, t0;
        if (isNaN(end)) return;
        function f(t) {
            t0 = t0 || t;
            var p = Math.min((t - t0) / 1000, 1);
            el.textContent = (end * (1 - Math.pow(1 - p, 3))).toFixed(dec);
            if (p < 1) requestAnimationFrame(f);
        }
        requestAnimationFrame(f);
    });
})();
</script>

@endsection