<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — ARAH</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $cur = old('role', 'mahasiswa');
        if (! in_array($cur, ['mahasiswa', 'dosen', 'admin'], true)) { $cur = 'mahasiswa'; }

        $ic = [
            'spark' => '<path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z"/>',
            'bars'  => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
            'chat'  => '<path d="M21 12a8 8 0 0 1-11.5 7.2L4 20l1-4.3A8 8 0 1 1 21 12z"/>',
            'trend' => '<path d="M3 17l6-6 4 4 8-8M15 7h6v6"/>',
            'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.5a3.5 3.5 0 0 1 0 7M18 14a6.5 6.5 0 0 1 3.5 6"/>',
            'doc'   => '<rect x="4" y="3" width="16" height="18" rx="3"/><path d="M8 8h8M8 12h8M8 16h5"/>',
            'user'  => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
            'book'  => '<path d="M4 5a2 2 0 0 1 2-2h13v16H6a2 2 0 0 0-2 2V5z"/><path d="M19 19v2H6"/>',
            'set'   => '<path d="M4 7h10M18 7h2M4 17h2M10 17h10"/><circle cx="16" cy="7" r="2"/><circle cx="8" cy="17" r="2"/>',
        ];
        $roles = [
            'mahasiswa' => [
                'tag' => 'Bimbingan akademik cerdas untuk membantu mahasiswa menemukan arah studi yang lebih tepat berdasarkan minat dan kemampuan akademik.',
                'feat' => [['Rekomendasi mata kuliah', 'spark'], ['Analisis akademik', 'bars'], ['Asisten AI NARA', 'chat'], ['Perencanaan studi', 'trend']],
            ],
            'dosen' => [
                'tag' => 'Dampingi mahasiswa bimbinganmu dengan data yang jelas, dari kondisi akademik sampai bahan diskusi perwalian.',
                'feat' => [['Mahasiswa bimbingan', 'users'], ['Pantau IPK dan nilai', 'bars'], ['Catatan perwalian', 'doc'], ['Insight dari NARA', 'chat']],
            ],
            'admin' => [
                'tag' => 'Kelola pengguna, data mata kuliah, dan jalannya sistem ARAH dari satu tempat.',
                'feat' => [['Kelola pengguna', 'user'], ['Data mata kuliah', 'book'], ['Laporan dan statistik', 'bars'], ['Pengaturan sistem', 'set']],
            ],
        ];
    @endphp

    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; }
        body { font-family: 'Poppins', Inter, ui-sans-serif, system-ui, sans-serif; color: #0f172a; background: #1E2A6B; }
        .ico { width: 16px; height: 16px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }
        [data-p] { display: none; }
        [data-p].on { display: block; animation: swap .35s ease; }
        @keyframes swap { from { opacity: 0; transform: translateY(8px); } }
        .panels { display: grid; }
        .panels > [data-p] { display: block; grid-area: 1 / 1; visibility: hidden; }
        .panels > [data-p].on { visibility: visible; }
        .stage { position: relative; overflow: hidden; min-height: 100vh; padding: 32px; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(160deg, rgba(15,30,90,.85), rgba(79,70,229,.65)), url('{{ asset('images/campus.jpg') }}') center / cover no-repeat, #1E2A6B; }
        .bg { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
        .orb { position: absolute; border-radius: 50%; filter: blur(70px); }
        .o1 { width: 460px; height: 460px; top: -140px; left: -120px; background: #7C3AED; opacity: .5; animation: d1 22s ease-in-out infinite alternate; }
        .o2 { width: 420px; height: 420px; bottom: -150px; right: -110px; background: #00B7F4; opacity: .38; animation: d2 26s ease-in-out infinite alternate; }
        @keyframes d1 { to { transform: translate(200px, 140px) scale(1.15); } }
        @keyframes d2 { to { transform: translate(-220px, -120px) scale(1.2); } }
        .grid { position: absolute; inset: 0; background-size: 56px 56px; animation: gm 40s linear infinite;
            background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
            -webkit-mask-image: radial-gradient(ellipse at center, #000 15%, transparent 75%); mask-image: radial-gradient(ellipse at center, #000 15%, transparent 75%); }
        @keyframes gm { to { background-position: 0 56px, 56px 0; } }
        .arrow { position: absolute; bottom: -60px; left: var(--x); width: var(--s); height: var(--s); opacity: 0; animation: rise var(--d) linear infinite; animation-delay: var(--delay); }
        .arrow::before { content: ""; position: absolute; inset: 0; border-radius: 3px; border-top: 2.5px solid rgba(255,255,255,.6); border-left: 2.5px solid rgba(255,255,255,.6); transform: rotate(45deg) scale(.7); }
        @keyframes rise { 0% { transform: translateY(0); opacity: 0; } 12%, 88% { opacity: .5; } 100% { transform: translateY(-115vh); opacity: 0; } }

        .card { position: relative; z-index: 2; width: min(1180px, 100%); min-height: 780px; display: grid; grid-template-columns: 46% 54%; overflow: hidden; border-radius: 30px; background: #fff; box-shadow: 0 30px 80px rgba(8,20,70,.5); animation: enter .6s ease both; }
        @keyframes enter { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }

       /* Kiri */
.left { 
    position: relative; 
    overflow: hidden; 
    padding: 36px 44px 16px; /* Ubah padding bawah dari 24px menjadi 16px */
    display: flex; 
    flex-direction: column; 
    justify-content: space-between; 
    background: #fff; 
}
.left::before { content: ""; position: absolute; width: 430px; height: 430px; top: -220px; right: -180px; border-radius: 50%; background: rgba(79,70,229,.05); }
.left > * { position: relative; z-index: 1; }
.logo { display: block; width: 150px; height: auto; margin-bottom: 12px; }
.tagline { max-width: 430px; margin: 0; color: #475569; font-size: 13px; line-height: 1.6; }

.features { margin-top: 16px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.feature { display: flex; align-items: center; gap: 10px; color: #1e293b; font-size: 12px; }
.fi { width: 30px; height: 30px; flex-shrink: 0; display: grid; place-items: center; border-radius: 8px; background: #eef2ff; border: 1px solid #e0e7ff; color: #4F46E5; }

/* Kotak preview dibuat agar jaraknya pas dan tidak terlalu memakan tempat ke bawah */
.preview { margin-top: 16px; padding: 14px 16px; border-radius: 14px; background: #f8f7ff; border: 1px solid #e5e3ff; }
.pv-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 11.5px; font-weight: 600; color: #1E2A6B; }
.pv-head em { font-style: normal; font-weight: 500; font-size: 10px; color: #4F46E5; background: #e8e6ff; padding: 2px 8px; border-radius: 99px; }
.pv-row + .pv-row { margin-top: 10px; }
.pv-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; font-size: 12px; color: #1e293b; }
.badge { padding: 2px 8px; border-radius: 99px; font-size: 10px; font-weight: 600; }
.badge.g { background: #d1fae5; color: #047857; }
.badge.a { background: #fef3c7; color: #b45309; }
.pv-bar { display: flex; align-items: center; gap: 10px; font-size: 10px; color: #64748b; }
.track { flex: 1; height: 6px; border-radius: 99px; background: #e8e6fb; overflow: hidden; }
.track i { display: block; height: 100%; width: var(--w); border-radius: 99px; background: linear-gradient(90deg, #4F46E5, #7C3AED); }

.stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
.stat { padding: 10px 6px; text-align: center; border-radius: 10px; background: #fff; border: 1px solid #e5e3ff; }
.stat b { display: block; color: #1E2A6B; font-size: 18px; line-height: 1.2; }
.stat span { color: #64748b; font-size: 10px; }

/* Bagian NARA didekatkan dan diberi ukuran yang proporsional */
.nara { 
    margin-top: 14px; /* Kurangi jarak atasnya */
    margin-bottom: 0px; /* Pastikan tidak ada sisa margin bawah */
    display: flex; 
    align-items: flex-end; 
    gap: 10px; 
}
.mascot { width: 140px; flex-shrink: 0; filter: drop-shadow(0 10px 18px rgba(30,42,107,.15)); animation: bob 4s ease-in-out infinite; }
.mascot img { display: block; width: 100%; height: auto; }
@keyframes bob { 0%, 100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-6px) rotate(2deg); } }

.bubble { margin-bottom: 16px; max-width: 290px; padding: 12px 14px; border-radius: 14px 14px 14px 4px; background: #fff; border: 1px solid #e0e7ff; box-shadow: 0 6px 18px rgba(79,70,229,.1); font-size: 12px; line-height: 1.5; color: #334155; }
.bubble b { color: #1E2A6B; }
.typing { display: flex; gap: 4px; margin-top: 6px; }
.typing i { width: 5px; height: 5px; border-radius: 50%; background: #a5b4fc; animation: blink 1.2s infinite; }
.typing i:nth-child(2) { animation-delay: .2s; }
.typing i:nth-child(3) { animation-delay: .4s; }
@keyframes blink { 0%, 80%, 100% { opacity: .3; transform: translateY(0); } 40% { opacity: 1; transform: translateY(-3px); } }

.left-foot { margin-top: 0; padding-top: 10px; border-top: 1px solid #f1f5f9; text-align: right; font-size: 9px; line-height: 1.4; color: #94a3b8; }

        /* Kanan: warna sedikit berbeda per peran */
        .right { position: relative; overflow: hidden; padding: 40px 64px 32px; display: flex; flex-direction: column; justify-content: center; background: linear-gradient(150deg, #2B2FA0 0%, #4F46E5 55%, #7C3AED 100%); }
        .card[data-role="dosen"] .right { background: linear-gradient(150deg, #1e3a8a 0%, #4F46E5 55%, #0ea5e9 100%); }
        .card[data-role="admin"] .right { background: linear-gradient(150deg, #1a1456 0%, #4338ca 50%, #a21caf 100%); }
        .right::before { content: ""; position: absolute; width: 380px; height: 380px; top: -180px; right: -160px; border-radius: 50%; background: rgba(255,255,255,.07); animation: pulse 8s ease-in-out infinite alternate; }
        @keyframes pulse { to { transform: scale(1.15); } }
        .right > * { position: relative; z-index: 2; }
        .heading { margin-bottom: 18px; }
        .heading h2 { margin: 0 0 6px; color: #fff; font-size: 29px; line-height: 1.2; font-weight: 700; letter-spacing: -.6px; }
        .heading p { margin: 0; max-width: 420px; color: rgba(255,255,255,.8); font-size: 13px; line-height: 1.7; }
        .status { margin-bottom: 14px; padding: 10px 12px; border-radius: 9px; background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.18); color: #fff; font-size: 11px; }

        fieldset { min-width: 0; margin: 0 0 16px; padding: 0; border: 0; }
        legend { padding: 0; }
        .seg { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .seg label { position: relative; cursor: pointer; }
        .seg input { position: absolute; opacity: 0; pointer-events: none; }
        .seg span { display: grid; place-items: center; height: 52px; border: 1px solid rgba(255,255,255,.28); border-radius: 14px; background: rgba(255,255,255,.14); color: #fff; font-size: 13px; font-weight: 600; transition: all .2s ease; }
        .seg span:hover { background: rgba(255,255,255,.22); }
        .seg input:checked + span { background: #fff; color: #4F46E5; box-shadow: 0 10px 22px rgba(10,10,60,.25); transform: translateY(-2px); }
        .seg input:focus-visible + span { outline: 3px solid rgba(255,255,255,.7); outline-offset: 2px; }

        .field { margin-bottom: 14px; }
        .lbl { display: block; margin-bottom: 6px; color: rgba(255,255,255,.92); font-size: 12px; font-weight: 600; }
        .iw { position: relative; }
        .lead { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); display: flex; color: #7a8ba5; pointer-events: none; transition: .2s; }
        .iw:focus-within .lead { color: #4F46E5; }
        .input { width: 100%; height: 48px; padding: 0 16px 0 46px; border: 1px solid rgba(255,255,255,.32); border-radius: 13px; outline: none; background: rgba(255,255,255,.96); color: #0f172a; font: inherit; font-size: 13px; transition: all .2s ease; }
        .input.has-eye { padding-right: 48px; }
        .input::placeholder { color: #94a3b8; }
        .input:focus { background: #fff; border-color: #fff; box-shadow: 0 0 0 4px rgba(255,255,255,.22); }
        .eye { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); width: 36px; height: 36px; border: 0; border-radius: 10px; display: grid; place-items: center; background: transparent; color: #7a8ba5; cursor: pointer; transition: .2s; }
        .eye:hover { color: #4F46E5; background: #eef2ff; }
        .error { margin-top: 6px; color: #ffe4e6; font-size: 11px; }

        .options { display: flex; align-items: center; justify-content: space-between; margin: 0 0 16px; }
        .remember { display: flex; align-items: center; gap: 8px; color: rgba(255,255,255,.85); font-size: 11px; cursor: pointer; }
        .remember input { width: 15px; height: 15px; margin: 0; accent-color: #4F46E5; cursor: pointer; }
        .forgot { color: #fff; font-size: 11px; text-decoration: underline; text-underline-offset: 3px; }

        .moodbox { margin-bottom: 16px; }
        .moodbox p { margin: 0 0 8px; font-size: 12px; font-weight: 600; color: rgba(255,255,255,.92); }
        .moodbox p span { font-weight: 400; color: rgba(255,255,255,.65); }
        .moods { display: grid; grid-template-columns: repeat(6, 1fr); gap: 7px; }
        .mood { display: flex; flex-direction: column; align-items: center; gap: 3px; padding: 8px 2px 7px; border: 1px solid rgba(255,255,255,.22); border-radius: 12px; background: rgba(255,255,255,.12); color: #fff; font: inherit; font-size: 10px; cursor: pointer; transition: all .2s ease; }
        .mood em { font-style: normal; font-size: 20px; line-height: 1; }
        .mood:hover { background: rgba(255,255,255,.22); transform: translateY(-2px); }
        .mood.on { background: #fff; color: #4F46E5; font-weight: 600; border-color: #fff; box-shadow: 0 8px 18px rgba(10,10,60,.25); transform: translateY(-2px); }

        .login-btn { width: 100%; height: 50px; border: 1px solid rgba(255,255,255,.2); border-radius: 14px; background: linear-gradient(135deg, #1E2A6B, #4F46E5); color: #fff; font: inherit; font-size: 14px; font-weight: 600; cursor: pointer; box-shadow: 0 12px 28px rgba(15,20,80,.45); transition: all .2s ease; }
        .login-btn:hover { transform: translateY(-1px); box-shadow: 0 16px 34px rgba(15,20,80,.55); }
        .login-btn:focus-visible, .mood:focus-visible { outline: 3px solid rgba(255,255,255,.7); outline-offset: 2px; }

        .register { margin-top: 18px; text-align: center; font-size: 11px; line-height: 1.7; color: rgba(255,255,255,.78); }
        .register a { margin-left: 4px; color: #fff; font-weight: 700; text-decoration: none; }
        .register a:hover { text-decoration: underline; text-underline-offset: 3px; }

        @media (prefers-reduced-motion: reduce) { .orb, .grid, .arrow, .mascot, .typing i, .right::before, [data-p].on { animation: none !important; } }
        @media (max-width: 980px) {
            .stage { padding: 22px; }
            .card { max-width: 680px; min-height: 0; grid-template-columns: 1fr; }
            .left { padding: 38px 38px 20px; }
            .right { padding: 40px 38px 32px; }
        }
        @media (max-width: 560px) {
            .stage { padding: 12px; }
            .card { border-radius: 22px; }
            .left { padding: 28px 22px 16px; }
            .right { padding: 32px 22px 26px; }
            .features { grid-template-columns: 1fr; gap: 9px; }
            .preview { display: none; }
            .mascot { width: 100px; }
            .heading h2 { font-size: 25px; }
            .moods { grid-template-columns: repeat(3, 1fr); }
        }
    </style>
</head>

<body>
<x-toast />

<div class="stage">

    <div class="bg" aria-hidden="true">
        <i class="orb o1"></i><i class="orb o2"></i>
        <div class="grid"></div>
        <i class="arrow" style="--x:6%;  --s:22px; --d:16s; --delay:-2s"></i>
        <i class="arrow" style="--x:30%; --s:26px; --d:18s; --delay:-5s"></i>
        <i class="arrow" style="--x:62%; --s:24px; --d:17s; --delay:-7s"></i>
        <i class="arrow" style="--x:90%; --s:20px; --d:21s; --delay:-12s"></i>
    </div>

    <div class="card" id="card" data-role="{{ $cur }}">

        {{-- ================= KIRI: berubah mengikuti peran ================= --}}
        <section class="left">
            <img src="{{ asset('images/LOGO.png') }}" alt="Logo ARAH" class="logo">
            <div class="panels">
            @foreach ($roles as $key => $r)
                <div data-p="{{ $key }}" class="{{ $cur === $key ? 'on' : '' }}">
                    <p class="tagline">{{ $r['tag'] }}</p>

                    <div class="features">
                        @foreach ($r['feat'] as $f)
                            <div class="feature"><span class="fi"><svg class="ico" viewBox="0 0 24 24">{!! $ic[$f[1]] !!}</svg></span><span>{{ $f[0] }}</span></div>
                        @endforeach
                    </div>

                    @if ($key === 'mahasiswa')
                        <div class="preview">
                            <div class="pv-head"><span>Contoh rekomendasi untukmu</span><em>Pratinjau</em></div>
                            <div class="pv-row">
                                <div class="pv-top"><b>Pemrograman Web</b><span class="badge g">Sangat Cocok</span></div>
                                <div class="pv-bar"><span class="track"><i style="--w:91%"></i></span><small>91%</small></div>
                            </div>
                            <div class="pv-row">
                                <div class="pv-top"><b>Data Mining</b><span class="badge a">Cocok</span></div>
                                <div class="pv-bar"><span class="track"><i style="--w:76%"></i></span><small>76%</small></div>
                            </div>
                        </div>
                    @elseif ($key === 'dosen')
                        <div class="preview">
                            <div class="pv-head"><span>Ringkasan mahasiswa bimbingan</span><em>Pratinjau</em></div>
                            <div class="pv-row">
                                <div class="pv-top"><b>Asesmen sudah diisi</b><span class="badge g">Baik</span></div>
                                <div class="pv-bar"><span class="track"><i style="--w:82%"></i></span><small>82%</small></div>
                            </div>
                            <div class="pv-row">
                                <div class="pv-top"><b>Perlu perhatian khusus</b><span class="badge a">Pantau</span></div>
                                <div class="pv-bar"><span class="track"><i style="--w:18%"></i></span><small>18%</small></div>
                            </div>
                        </div>
                    @else
                        <div class="preview">
                            <div class="pv-head"><span>Ringkasan sistem</span><em>Pratinjau</em></div>
                            <div class="stats">
                                <div class="stat"><b>1.248</b><span>Mahasiswa</span></div>
                                <div class="stat"><b>64</b><span>Dosen</span></div>
                                <div class="stat"><b>212</b><span>Mata kuliah</span></div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
            </div>

            <div class="nara">
                <div class="mascot"><img src="{{ asset('images/nyapa.png') }}" alt="Maskot NARA"></div>
                <div class="bubble">
                    <span id="nara-msg"></span>
                    <div class="typing"><i></i><i></i><i></i></div>
                </div>
            </div>

            <div class="left-foot">ARAH — Academic Recommendation &amp; Assistance Hub</div>
        </section>


        {{-- ================= KANAN ================= --}}
        <section class="right">

            <div class="heading">
                <h2>Selamat datang kembali</h2>
                <p data-p="mahasiswa" class="{{ $cur === 'mahasiswa' ? 'on' : '' }}">Masuk untuk melanjutkan perjalanan akademikmu bersama ARAH.</p>
                <p data-p="dosen" class="{{ $cur === 'dosen' ? 'on' : '' }}">Masuk untuk mendampingi mahasiswa bimbinganmu bersama ARAH.</p>
                <p data-p="admin" class="{{ $cur === 'admin' ? 'on' : '' }}">Masuk untuk mengelola data dan sistem ARAH.</p>
            </div>

            @if (session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="mood" id="mood" value="">

                <fieldset>
                    <legend class="lbl">Masuk sebagai</legend>
                    <div class="seg">
                        @foreach (['mahasiswa' => 'Mahasiswa', 'dosen' => 'Dosen', 'admin' => 'Admin'] as $v => $label)
                            <label><input type="radio" name="role" value="{{ $v }}" @checked($cur === $v)><span>{{ $label }}</span></label>
                        @endforeach
                    </div>
                </fieldset>

                <div class="field">
                    <label for="email" class="lbl">Email</label>
                    <div class="iw">
                        <span class="lead"><svg class="ico" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 8l9 6 9-6"/></svg></span>
                        <input id="email" class="input" type="email" name="email" value="{{ old('email') }}"
                               placeholder="Masukkan email kamu" required autofocus autocomplete="username">
                    </div>
                    @error('email')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="password" class="lbl">Password</label>
                    <div class="iw">
                        <span class="lead"><svg class="ico" viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="9" rx="3"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg></span>
                        <input id="password" class="input has-eye" type="password" name="password"
                               placeholder="Masukkan password kamu" required autocomplete="current-password">
                        <button type="button" id="toggle-pw" class="eye" aria-label="Tampilkan password">
                            <svg class="ico" viewBox="0 0 24 24" style="width:18px;height:18px"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="options">
                    <label for="remember_me" class="remember">
                        <input id="remember_me" type="checkbox" name="remember">
                        <span>Ingat saya</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot">Lupa password?</a>
                    @endif
                </div>

                <div class="moodbox">
                    <p>Gimana moodmu hari ini? <span>(opsional)</span></p>
                    <div class="moods">
                        <button type="button" class="mood" data-mood="semangat" title="Semangat"><em>🤩</em>Semangat</button>
                        <button type="button" class="mood" data-mood="senang" title="Senang"><em>😊</em>Senang</button>
                        <button type="button" class="mood" data-mood="biasa" title="Biasa"><em>😐</em>Biasa</button>
                        <button type="button" class="mood" data-mood="lelah" title="Lelah"><em>🥱</em>Lelah</button>
                        <button type="button" class="mood" data-mood="stres" title="Stres"><em>😣</em>Stres</button>
                        <button type="button" class="mood" data-mood="sedih" title="Sedih"><em>😢</em>Sedih</button>
                    </div>
                </div>

                <button type="submit" class="login-btn">Masuk ke ARAH</button>
            </form>

            {{-- Tanpa login Google. Arahan ke pembuatan akun hanya untuk mahasiswa --}}
            <div class="register" data-p="mahasiswa" style="{{ $cur === 'mahasiswa' ? '' : 'display:none' }}" class="{{ $cur === 'mahasiswa' ? 'on' : '' }}">
                @if (Route::has('register'))
                    <span>Belum punya akun?</span><a href="{{ route('register') }}">Daftar sekarang</a>
                @endif
            </div>
            <div class="register" data-p="dosen">Akun dosen dibuat oleh admin kampus.<br>Belum punya akun? Hubungi admin.</div>
            <div class="register" data-p="admin">Akun admin dikelola oleh pengelola sistem ARAH.</div>

        </section>
    </div>
</div>

<script>
    document.getElementById('toggle-pw').addEventListener('click', function () {
        var input = document.getElementById('password');
        var hidden = input.type === 'password';
        input.type = hidden ? 'text' : 'password';
        this.setAttribute('aria-label', hidden ? 'Sembunyikan password' : 'Tampilkan password');
    });

    var card = document.getElementById('card'), email = document.getElementById('email');
    var msgEl = document.getElementById('nara-msg'), moodEl = document.getElementById('mood');
    var chips = document.querySelectorAll('.mood');
    var role = card.dataset.role;

    var hello = {
        mahasiswa: '<b>Halo, aku NARA!</b> Aku siap menjelaskan alasan di balik setiap rekomendasi mata kuliahmu.',
        dosen: '<b>Halo, Bapak/Ibu Dosen!</b> Aku bantu merangkum kondisi mahasiswa bimbinganmu.',
        admin: '<b>Halo, Admin!</b> Aku siap membantu memantau penggunaan ARAH.'
    };
    var holder = { mahasiswa: 'Masukkan email kamu', dosen: 'Masukkan email dosen', admin: 'Masukkan email admin' };
    var msgs = {
        mahasiswa: {
            semangat: 'Semangat banget! Yuk lihat rekomendasi mata kuliah yang cocok buatmu hari ini.',
            senang: 'Senang dengarnya! Semoga harimu lancar ya.',
            biasa: 'Oke, santai saja. Aku siap bantu kapan pun kamu butuh.',
            lelah: 'Istirahat dulu kalau perlu. Nanti kita lihat rekomendasinya pelan-pelan.',
            stres: 'Tarik napas dulu. Kita rapikan rencana studimu sedikit demi sedikit ya.',
            sedih: 'Aku di sini. Kalau butuh, Dosen PA kamu juga siap membantu.'
        },
        staff: {
            semangat: 'Semangat banget! Semoga harimu produktif.',
            senang: 'Senang dengarnya! Semoga harimu lancar ya.',
            biasa: 'Oke, santai saja. Aku siap bantu kapan pun dibutuhkan.',
            lelah: 'Istirahat sejenak kalau perlu. Pekerjaannya bisa dilanjut pelan-pelan.',
            stres: 'Tarik napas dulu. Kita selesaikan satu per satu ya.',
            sedih: 'Aku di sini. Semoga harimu segera membaik.'
        }
    };

    function applyRole(r) {
        role = r;
        card.dataset.role = r;
        document.querySelectorAll('[data-p]').forEach(function (e) {
            var on = e.dataset.p === r;
            e.style.display = '';
            e.classList.toggle('on', on);
        });
        email.placeholder = holder[r];
        chips.forEach(function (c) { c.classList.remove('on'); });
        moodEl.value = '';
        msgEl.innerHTML = hello[r];
    }

    document.querySelectorAll('input[name="role"]').forEach(function (rb) {
        rb.addEventListener('change', function () { if (rb.checked) applyRole(rb.value); });
    });

    chips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            var wasOn = chip.classList.contains('on');
            chips.forEach(function (c) { c.classList.remove('on'); });
            moodEl.value = '';
            msgEl.innerHTML = hello[role];
            if (!wasOn) {
                chip.classList.add('on');
                moodEl.value = chip.dataset.mood;
                msgEl.textContent = msgs[role === 'mahasiswa' ? 'mahasiswa' : 'staff'][chip.dataset.mood];
            }
        });
    });

    applyRole(role);
</script>

</body>
</html>