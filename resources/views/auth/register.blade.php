<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar — ARAH</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; }
        body { font-family: 'Poppins', Inter, ui-sans-serif, system-ui, sans-serif; color: #0f172a; background: #1E2A6B; }
        .ico { width: 16px; height: 16px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }

        /* ===== Latar halaman ===== */
        .stage { position: relative; overflow: hidden; min-height: 100vh; padding: 32px; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(160deg, rgba(15, 30, 90, .85), rgba(79, 70, 229, .65)), url('{{ asset('images/campus.jpg') }}') center / cover no-repeat, #1E2A6B; }
        .bg { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
        .orb { position: absolute; border-radius: 50%; filter: blur(70px); will-change: transform; }
        .o1 { width: 460px; height: 460px; top: -140px; left: -120px; background: #7C3AED; opacity: .5; animation: drift1 22s ease-in-out infinite alternate; }
        .o2 { width: 420px; height: 420px; bottom: -150px; right: -110px; background: #00B7F4; opacity: .38; animation: drift2 26s ease-in-out infinite alternate; }
        .o3 { width: 320px; height: 320px; top: 38%; left: 58%; background: #4F46E5; opacity: .45; animation: drift3 30s ease-in-out infinite alternate; }
        @keyframes drift1 { to { transform: translate(200px, 140px) scale(1.15); } }
        @keyframes drift2 { to { transform: translate(-220px, -120px) scale(1.2); } }
        @keyframes drift3 { to { transform: translate(-260px, 90px) scale(.85); } }
        .grid { position: absolute; inset: 0; background-size: 56px 56px; animation: gridmove 40s linear infinite;
            background-image: linear-gradient(rgba(255, 255, 255, .05) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, .05) 1px, transparent 1px);
            -webkit-mask-image: radial-gradient(ellipse at center, #000 15%, transparent 75%); mask-image: radial-gradient(ellipse at center, #000 15%, transparent 75%); }
        @keyframes gridmove { to { background-position: 0 56px, 56px 0; } }
        .arrow { position: absolute; bottom: -60px; left: var(--x); width: var(--s); height: var(--s); opacity: 0; animation: rise var(--d) linear infinite; animation-delay: var(--delay); }
        .arrow::before { content: ""; position: absolute; inset: 0; border-radius: 3px; border-top: 2.5px solid rgba(255, 255, 255, .6); border-left: 2.5px solid rgba(255, 255, 255, .6); transform: rotate(45deg) scale(.7); }
        @keyframes rise { 0% { transform: translateY(0) rotate(0); opacity: 0; } 12%, 88% { opacity: .5; } 100% { transform: translateY(-115vh) rotate(14deg); opacity: 0; } }

        /* ===== Kartu ===== */
        .card { position: relative; z-index: 2; width: min(1180px, 100%); display: grid; grid-template-columns: 46% 54%; overflow: hidden; border-radius: 30px; background: #fff; box-shadow: 0 30px 80px rgba(8, 20, 70, .5); animation: enter .6s ease both; }
        @keyframes enter { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }

        /* ===== Kiri ===== */
        .left { position: relative; overflow: hidden; padding: 44px 52px 22px; display: flex; flex-direction: column; background: #fff; }
        .left::before { content: ""; position: absolute; width: 430px; height: 430px; top: -220px; right: -180px; border-radius: 50%; background: rgba(79, 70, 229, .05); }
        .left::after { content: ""; position: absolute; width: 330px; height: 330px; left: -140px; bottom: -210px; border-radius: 50%; border: 1px solid rgba(79, 70, 229, .1); }
        .left > * { position: relative; z-index: 1; }
        .logo { display: block; width: 170px; height: auto; margin-bottom: 18px; }
        .tagline { max-width: 430px; margin: 0; color: #475569; font-size: 13.5px; line-height: 1.75; }
        .features { margin-top: 22px; display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .feature { display: flex; align-items: center; gap: 10px; color: #1e293b; font-size: 12px; }
        .fi { width: 32px; height: 32px; flex-shrink: 0; display: grid; place-items: center; border-radius: 9px; background: #eef2ff; border: 1px solid #e0e7ff; color: #4F46E5; }

        .steps { margin-top: 22px; padding: 16px 18px; border-radius: 16px; background: #f8f7ff; border: 1px solid #e5e3ff; }
        .steps h4 { margin: 0 0 12px; font-size: 11px; font-weight: 600; color: #1E2A6B; }
        .steps ol { margin: 0; padding: 0; list-style: none; }
        .steps li { display: flex; align-items: center; gap: 12px; font-size: 12px; color: #1e293b; }
        .steps li + li { margin-top: 11px; }
        .steps li b { width: 24px; height: 24px; flex-shrink: 0; display: grid; place-items: center; border-radius: 50%; background: linear-gradient(135deg, #4F46E5, #7C3AED); color: #fff; font-size: 11px; }

        .nara { margin-top: auto; padding-top: 14px; display: flex; align-items: flex-end; gap: 8px; }
        .mascot { width: 130px; flex-shrink: 0; filter: drop-shadow(0 12px 20px rgba(30, 42, 107, .18)); animation: bob 4s ease-in-out infinite; }
        .mascot img { display: block; width: 100%; height: auto; }
        @keyframes bob { 0%, 100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-8px) rotate(2deg); } }
        .bubble { margin-bottom: 26px; max-width: 290px; padding: 11px 15px; border-radius: 16px 16px 16px 4px; background: #fff; border: 1px solid #e0e7ff; box-shadow: 0 8px 22px rgba(79, 70, 229, .12); font-size: 12px; line-height: 1.55; color: #334155; }
        .bubble b { color: #1E2A6B; }
        .typing { display: flex; gap: 4px; margin-top: 7px; }
        .typing i { width: 6px; height: 6px; border-radius: 50%; background: #a5b4fc; animation: blink 1.2s infinite; }
        .typing i:nth-child(2) { animation-delay: .2s; }
        .typing i:nth-child(3) { animation-delay: .4s; }
        @keyframes blink { 0%, 80%, 100% { opacity: .3; transform: translateY(0); } 40% { opacity: 1; transform: translateY(-3px); } }
        .left-foot { text-align: right; font-size: 9px; line-height: 1.5; color: #94a3b8; }

        /* ===== Kanan ===== */
        .right { position: relative; overflow: hidden; padding: 40px 64px 32px; display: flex; flex-direction: column; justify-content: center; background: linear-gradient(150deg, #2B2FA0 0%, #4F46E5 55%, #7C3AED 100%); }
        .right::before { content: ""; position: absolute; width: 380px; height: 380px; top: -180px; right: -160px; border-radius: 50%; background: rgba(255, 255, 255, .07); animation: pulse 8s ease-in-out infinite alternate; }
        .right::after { content: ""; position: absolute; width: 300px; height: 300px; left: -170px; bottom: -170px; border-radius: 50%; border: 1px solid rgba(255, 255, 255, .1); }
        @keyframes pulse { to { transform: scale(1.15); } }
        .right > * { position: relative; z-index: 2; }

        .heading { margin-bottom: 20px; }
        .heading h2 { margin: 0 0 6px; color: #fff; font-size: 29px; line-height: 1.2; font-weight: 700; letter-spacing: -.6px; }
        .heading p { margin: 0; max-width: 420px; color: rgba(255, 255, 255, .8); font-size: 13px; line-height: 1.7; }

        .field { margin-bottom: 13px; }
        .lbl { display: block; margin-bottom: 6px; color: rgba(255, 255, 255, .92); font-size: 12px; font-weight: 600; }
        .iw { position: relative; }
        .lead { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); display: flex; color: #7a8ba5; pointer-events: none; transition: .2s; }
        .iw:focus-within .lead { color: #4F46E5; }
        .input { width: 100%; height: 47px; padding: 0 16px 0 46px; border: 1px solid rgba(255, 255, 255, .32); border-radius: 13px; outline: none; background: rgba(255, 255, 255, .96); color: #0f172a; font: inherit; font-size: 13px; transition: all .2s ease; }
        .input.has-eye { padding-right: 48px; }
        .input::placeholder { color: #94a3b8; }
        .input:hover { border-color: rgba(255, 255, 255, .55); }
        .input:focus { background: #fff; border-color: #fff; box-shadow: 0 0 0 4px rgba(255, 255, 255, .22); }
        .eye { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); width: 36px; height: 36px; border: 0; border-radius: 10px; display: grid; place-items: center; background: transparent; color: #7a8ba5; cursor: pointer; transition: .2s; }
        .eye:hover { color: #4F46E5; background: #eef2ff; }
        .error { margin-top: 6px; color: #ffe4e6; font-size: 11px; }

        .submit { width: 100%; height: 50px; margin-top: 8px; border: 1px solid rgba(255, 255, 255, .2); border-radius: 14px; background: linear-gradient(135deg, #1E2A6B, #4F46E5); color: #fff; font: inherit; font-size: 14px; font-weight: 600; cursor: pointer; box-shadow: 0 12px 28px rgba(15, 20, 80, .45); transition: all .2s ease; }
        .submit:hover { transform: translateY(-1px); box-shadow: 0 16px 34px rgba(15, 20, 80, .55); }
        .submit:active { transform: none; }

        .switch { margin-top: 20px; text-align: center; font-size: 11px; color: rgba(255, 255, 255, .75); }
        .switch a { margin-left: 4px; color: #fff; font-weight: 700; text-decoration: none; }
        .switch a:hover { text-decoration: underline; text-underline-offset: 3px; }

        @media (prefers-reduced-motion: reduce) { .orb, .grid, .arrow, .mascot, .typing i, .right::before { animation: none !important; } }
        @media (max-width: 980px) {
            .stage { padding: 22px; }
            .card { max-width: 680px; grid-template-columns: 1fr; }
            .left { padding: 38px 38px 20px; }
            .right { padding: 40px 38px 32px; }
        }
        @media (max-width: 560px) {
            .stage { padding: 12px; }
            .card { border-radius: 22px; }
            .left { padding: 28px 22px 16px; }
            .right { padding: 32px 22px 26px; }
            .features { grid-template-columns: 1fr; gap: 9px; }
            .steps { display: none; }
            .mascot { width: 100px; }
            .heading h2 { font-size: 25px; }
        }
    </style>
</head>

<body>
<x-toast />

<div class="stage">

    <div class="bg" aria-hidden="true">
        <i class="orb o1"></i><i class="orb o2"></i><i class="orb o3"></i>
        <div class="grid"></div>
        <i class="arrow" style="--x:6%;  --s:22px; --d:16s; --delay:-2s"></i>
        <i class="arrow" style="--x:18%; --s:14px; --d:20s; --delay:-9s"></i>
        <i class="arrow" style="--x:30%; --s:26px; --d:18s; --delay:-5s"></i>
        <i class="arrow" style="--x:62%; --s:24px; --d:17s; --delay:-7s"></i>
        <i class="arrow" style="--x:86%; --s:28px; --d:19s; --delay:-11s"></i>
        <i class="arrow" style="--x:94%; --s:18px; --d:23s; --delay:-16s"></i>
    </div>

    <div class="card">

        <!-- ================= KIRI ================= -->
        <section class="left">
            <img src="{{ asset('images/LOGO.png') }}" alt="Logo ARAH" class="logo">

            <p class="tagline">
                Buat akunmu dan mulai temukan arah studi yang lebih tepat
                berdasarkan minat dan kemampuan akademikmu.
            </p>

            <div class="features">
                <div class="feature"><span class="fi"><svg class="ico" viewBox="0 0 24 24"><path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z"/></svg></span><span>Rekomendasi mata kuliah</span></div>
                <div class="feature"><span class="fi"><svg class="ico" viewBox="0 0 24 24"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg></span><span>Analisis akademik</span></div>
                <div class="feature"><span class="fi"><svg class="ico" viewBox="0 0 24 24"><path d="M21 12a8 8 0 0 1-11.5 7.2L4 20l1-4.3A8 8 0 1 1 21 12z"/></svg></span><span>Asisten AI NARA</span></div>
                <div class="feature"><span class="fi"><svg class="ico" viewBox="0 0 24 24"><path d="M3 17l6-6 4 4 8-8M15 7h6v6"/></svg></span><span>Perencanaan studi</span></div>
            </div>

            <div class="steps">
                <h4>Mulai dalam 3 langkah</h4>
                <ol>
                    <li><b>1</b><span>Buat akun ARAH</span></li>
                    <li><b>2</b><span>Isi asesmen minat dan kemampuan</span></li>
                    <li><b>3</b><span>Dapatkan rekomendasi mata kuliah beserta alasannya</span></li>
                </ol>
            </div>

            <div class="nara">
                <div class="mascot"><img src="{{ asset('images/nyapa.png') }}" alt="Maskot NARA"></div>
                <div class="bubble">
                    <b>Halo, aku NARA!</b> Isi data singkat dulu ya, nanti aku bantu cari mata kuliah yang cocok buatmu.
                    <div class="typing"><i></i><i></i><i></i></div>
                </div>
            </div>

            <div class="left-foot">ARAH — Academic Recommendation &amp; Assistance Hub</div>
        </section>


        <!-- ================= KANAN ================= -->
        <section class="right">

            <div class="heading">
                <h2>Buat akun ARAH</h2>
                <p>Isi data singkat di bawah ini untuk mulai menemukan arah akademikmu.</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="field">
                    <label for="name" class="lbl">Nama lengkap</label>
                    <div class="iw">
                        <span class="lead"><svg class="ico" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg></span>
                        <input id="name" class="input" type="text" name="name" value="{{ old('name') }}"
                               placeholder="Masukkan nama lengkap" required autofocus autocomplete="name">
                    </div>
                    @error('name')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="email" class="lbl">Email</label>
                    <div class="iw">
                        <span class="lead"><svg class="ico" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 8l9 6 9-6"/></svg></span>
                        <input id="email" class="input" type="email" name="email" value="{{ old('email') }}"
                               placeholder="Masukkan email kamu" required autocomplete="username">
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
                               placeholder="Minimal 8 karakter" required autocomplete="new-password">
                        <button type="button" class="eye" data-for="password" aria-label="Tampilkan password">
                            <svg class="ico" viewBox="0 0 24 24" style="width:18px;height:18px"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="password_confirmation" class="lbl">Konfirmasi password</label>
                    <div class="iw">
                        <span class="lead"><svg class="ico" viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="9" rx="3"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg></span>
                        <input id="password_confirmation" class="input has-eye" type="password" name="password_confirmation"
                               placeholder="Ulangi password" required autocomplete="new-password">
                        <button type="button" class="eye" data-for="password_confirmation" aria-label="Tampilkan password">
                            <svg class="ico" viewBox="0 0 24 24" style="width:18px;height:18px"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="submit">Daftar sekarang</button>
            </form>

            <div class="switch">
                <span>Sudah punya akun?</span>
                <a href="{{ route('login') }}">Masuk</a>
            </div>

        </section>
    </div>
</div>

<script>
    document.querySelectorAll('.eye').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(btn.dataset.for);
            var hidden = input.type === 'password';
            input.type = hidden ? 'text' : 'password';
            btn.setAttribute('aria-label', hidden ? 'Sembunyikan password' : 'Tampilkan password');
        });
    });
</script>

</body>
</html>