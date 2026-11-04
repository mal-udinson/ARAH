<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password — ARAH</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; }
        body { font-family: 'Poppins', Inter, ui-sans-serif, system-ui, sans-serif; color: #0f172a; background: #1E2A6B; }
        .ico { width: 16px; height: 16px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }

        /* ===== Latar (tema sama dengan login & daftar) ===== */
        .stage { position: relative; overflow: hidden; min-height: 100vh; padding: 28px 16px; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(160deg, rgba(15, 30, 90, .85), rgba(79, 70, 229, .65)), url('{{ asset('images/campus.jpg') }}') center / cover no-repeat, #1E2A6B; }
        .bg { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
        .orb { position: absolute; border-radius: 50%; filter: blur(70px); will-change: transform; }
        .o1 { width: 440px; height: 440px; top: -140px; left: -120px; background: #7C3AED; opacity: .5; animation: d1 22s ease-in-out infinite alternate; }
        .o2 { width: 400px; height: 400px; bottom: -150px; right: -110px; background: #00B7F4; opacity: .38; animation: d2 26s ease-in-out infinite alternate; }
        @keyframes d1 { to { transform: translate(200px, 140px) scale(1.15); } }
        @keyframes d2 { to { transform: translate(-220px, -120px) scale(1.2); } }
        .grid { position: absolute; inset: 0; background-size: 56px 56px; animation: gm 40s linear infinite;
            background-image: linear-gradient(rgba(255, 255, 255, .05) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, .05) 1px, transparent 1px);
            -webkit-mask-image: radial-gradient(ellipse at center, #000 15%, transparent 75%); mask-image: radial-gradient(ellipse at center, #000 15%, transparent 75%); }
        @keyframes gm { to { background-position: 0 56px, 56px 0; } }
        .arrow { position: absolute; bottom: -60px; left: var(--x); width: var(--s); height: var(--s); opacity: 0; animation: rise var(--d) linear infinite; animation-delay: var(--delay); }
        .arrow::before { content: ""; position: absolute; inset: 0; border-radius: 3px; border-top: 2.5px solid rgba(255, 255, 255, .6); border-left: 2.5px solid rgba(255, 255, 255, .6); transform: rotate(45deg) scale(.7); }
        @keyframes rise { 0% { transform: translateY(0) rotate(0); opacity: 0; } 12%, 88% { opacity: .5; } 100% { transform: translateY(-115vh) rotate(14deg); opacity: 0; } }

        /* ===== Satu kartu terpusat, maskot mengintip dari atas ===== */
        .wrap { position: relative; z-index: 2; width: min(460px, 100%); display: flex; flex-direction: column; align-items: center; }
        .mascot { position: relative; z-index: 3; width: 118px; margin-bottom: -38px; filter: drop-shadow(0 14px 22px rgba(8, 20, 70, .45)); animation: bob 4s ease-in-out infinite; }
        .mascot img { display: block; width: 100%; height: auto; }
        @keyframes bob { 0%, 100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-8px) rotate(2deg); } }

        .card { position: relative; width: 100%; overflow: hidden; padding: 52px 40px 26px; border-radius: 28px; background: #fff; text-align: center; box-shadow: 0 30px 80px rgba(8, 20, 70, .5); animation: enter .6s ease both; }
        .card::before { content: ""; position: absolute; inset: 0 0 auto; height: 6px; background: linear-gradient(90deg, #4F46E5, #7C3AED, #00B7F4); }
        @keyframes enter { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }

        .logo { display: block; width: 104px; height: auto; margin: 0 auto 14px; }
        h1 { margin: 0 0 8px; color: #1E2A6B; font-size: 25px; font-weight: 700; letter-spacing: -.5px; }
        .sub { margin: 0 auto 22px; max-width: 340px; color: #64748b; font-size: 13px; line-height: 1.7; }

        .ok { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 16px; padding: 11px 13px; border-radius: 12px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; font-size: 12px; line-height: 1.55; text-align: left; }
        .ok .ico { margin-top: 2px; }

        form { text-align: left; }
        .lbl { display: block; margin-bottom: 6px; color: #1e293b; font-size: 12px; font-weight: 600; }
        .iw { position: relative; }
        .lead { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); display: flex; color: #7a8ba5; pointer-events: none; transition: .2s; }
        .iw:focus-within .lead { color: #4F46E5; }
        .input { width: 100%; height: 48px; padding: 0 16px 0 46px; border: 1px solid #e0e7ff; border-radius: 13px; outline: none; background: #f8f7ff; color: #0f172a; font: inherit; font-size: 13px; transition: all .2s ease; }
        .input::placeholder { color: #94a3b8; }
        .input:hover { border-color: #c7d2fe; }
        .input:focus { background: #fff; border-color: #4F46E5; box-shadow: 0 0 0 4px rgba(79, 70, 229, .15); }
        .error { margin-top: 6px; color: #dc2626; font-size: 11px; }

        .btn { width: 100%; height: 50px; margin-top: 16px; border: 0; border-radius: 14px; background: linear-gradient(135deg, #1E2A6B, #4F46E5); color: #fff; font: inherit; font-size: 14px; font-weight: 600; cursor: pointer; box-shadow: 0 12px 26px rgba(30, 42, 107, .35); transition: all .2s ease; }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 16px 32px rgba(30, 42, 107, .45); }
        .btn:active { transform: none; }
        .btn:focus-visible, .back:focus-visible { outline: 3px solid rgba(79, 70, 229, .45); outline-offset: 3px; }

        /* Urutan yang memang berurutan, jadi pakai nomor */
        .steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-top: 22px; padding: 14px 10px; border-radius: 16px; background: #f8f7ff; border: 1px solid #e5e3ff; }
        .st { display: flex; flex-direction: column; align-items: center; gap: 7px; color: #475569; font-size: 11px; line-height: 1.4; }
        .st b { width: 24px; height: 24px; display: grid; place-items: center; border-radius: 50%; background: linear-gradient(135deg, #4F46E5, #7C3AED); color: #fff; font-size: 11px; }

        .back { display: inline-flex; align-items: center; gap: 6px; margin-top: 18px; color: #4F46E5; font-size: 12px; font-weight: 600; text-decoration: none; border-radius: 6px; }
        .back:hover { text-decoration: underline; text-underline-offset: 3px; }
        .foot { margin: 16px 0 0; color: rgba(255, 255, 255, .6); font-size: 10px; text-align: center; }

        @media (prefers-reduced-motion: reduce) { .orb, .grid, .arrow, .mascot, .card { animation: none !important; } }
        @media (max-width: 480px) {
            .card { padding: 50px 22px 22px; border-radius: 22px; }
            h1 { font-size: 22px; }
            .mascot { width: 100px; margin-bottom: -32px; }
        }
    </style>
</head>

<body>
<x-toast />

<div class="stage">

    <div class="bg" aria-hidden="true">
        <i class="orb o1"></i><i class="orb o2"></i>
        <div class="grid"></div>
        <i class="arrow" style="--x:8%;  --s:22px; --d:16s; --delay:-2s"></i>
        <i class="arrow" style="--x:28%; --s:16px; --d:20s; --delay:-9s"></i>
        <i class="arrow" style="--x:72%; --s:26px; --d:18s; --delay:-6s"></i>
        <i class="arrow" style="--x:92%; --s:18px; --d:23s; --delay:-14s"></i>
    </div>

    <main class="wrap">

        <div class="mascot"><img src="{{ asset('images/nyapa.png') }}" alt="Maskot NARA"></div>

        <section class="card">
            <img src="{{ asset('images/LOGO.png') }}" alt="Logo ARAH" class="logo">

            <h1>Lupa password?</h1>
            <p class="sub">Tenang, itu wajar. Masukkan email akunmu dan kami kirim tautan untuk membuat password baru.</p>

            @if (session('status'))
                <div class="ok" role="status">
                    <svg class="ico" viewBox="0 0 24 24"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                    <span>Tautan reset sudah dikirim. Cek inbox emailmu, dan folder spam kalau belum muncul.</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <label for="email" class="lbl">Email akun</label>
                <div class="iw">
                    <span class="lead"><svg class="ico" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 8l9 6 9-6"/></svg></span>
                    <input id="email" class="input" type="email" name="email" value="{{ old('email') }}"
                           placeholder="Masukkan email yang kamu daftarkan" required autofocus autocomplete="username">
                </div>
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror

                <button type="submit" class="btn">Kirim tautan reset</button>
            </form>

            <div class="steps">
                <div class="st"><b>1</b><span>Isi email akunmu</span></div>
                <div class="st"><b>2</b><span>Buka tautan di inbox</span></div>
                <div class="st"><b>3</b><span>Buat password baru</span></div>
            </div>

            <a href="{{ route('login') }}" class="back">
                <svg class="ico" viewBox="0 0 24 24"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
                Kembali ke halaman masuk
            </a>
        </section>

        <p class="foot">ARAH — Academic Recommendation &amp; Assistance Hub</p>
    </main>
</div>

</body>
</html>