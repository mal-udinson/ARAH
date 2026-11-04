<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lengkapi Data Diri — ARAH</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; }
        body { font-family: 'Poppins', Inter, ui-sans-serif, system-ui, sans-serif; color: #0f172a; background: #1E2A6B; }
        .ico { width: 16px; height: 16px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }

        .stage { position: relative; overflow: hidden; min-height: 100vh; padding: 28px 16px; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(160deg, rgba(15,30,90,.85), rgba(79,70,229,.65)), url('{{ asset('images/campus.jpg') }}') center / cover no-repeat, #1E2A6B; }
        .bg { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
        .orb { position: absolute; border-radius: 50%; filter: blur(70px); }
        .o1 { width: 440px; height: 440px; top: -140px; left: -120px; background: #7C3AED; opacity: .5; animation: d1 22s ease-in-out infinite alternate; }
        .o2 { width: 400px; height: 400px; bottom: -150px; right: -110px; background: #00B7F4; opacity: .38; animation: d1 26s ease-in-out infinite alternate-reverse; }
        @keyframes d1 { to { transform: translate(160px, 100px) scale(1.15); } }
        .grid { position: absolute; inset: 0; background-size: 56px 56px; animation: gm 40s linear infinite;
            background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
            -webkit-mask-image: radial-gradient(ellipse at center, #000 15%, transparent 75%); mask-image: radial-gradient(ellipse at center, #000 15%, transparent 75%); }
        @keyframes gm { to { background-position: 0 56px, 56px 0; } }

        .wrap { position: relative; z-index: 2; width: min(560px, 100%); display: flex; flex-direction: column; align-items: center; }
        .mascot { position: relative; z-index: 3; width: 112px; margin-bottom: -36px; filter: drop-shadow(0 14px 22px rgba(8,20,70,.45)); animation: bob 4s ease-in-out infinite; }
        .mascot img { display: block; width: 100%; height: auto; }
        @keyframes bob { 0%, 100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-8px) rotate(2deg); } }

        .card { position: relative; width: 100%; overflow: hidden; padding: 50px 40px 24px; border-radius: 28px; background: #fff; box-shadow: 0 30px 80px rgba(8,20,70,.5); animation: enter .6s ease both; }
        .card::before { content: ""; position: absolute; inset: 0 0 auto; height: 6px; background: linear-gradient(90deg, #4F46E5, #7C3AED, #00B7F4); }
        @keyframes enter { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }

        /* Langkah */
        .steps { display: flex; align-items: center; justify-content: center; gap: 8px; margin: 0 0 18px; padding: 0; list-style: none; }
        .steps li { display: flex; align-items: center; gap: 7px; color: #94a3b8; font-size: 11px; font-weight: 500; }
        .steps b { width: 22px; height: 22px; display: grid; place-items: center; border-radius: 50%; background: #eef2ff; color: #94a3b8; font-size: 10.5px; }
        .steps li.done b { color: #fff; background: #10b981; }
        .steps li.now { color: #1E2A6B; font-weight: 600; }
        .steps li.now b { color: #fff; background: linear-gradient(135deg, #4F46E5, #7C3AED); box-shadow: 0 0 0 4px rgba(79,70,229,.15); }
        .steps i { width: 26px; height: 2px; border-radius: 2px; background: #e5e3ff; }

        h1 { margin: 0 0 8px; color: #1E2A6B; font-size: 24px; font-weight: 700; letter-spacing: -.5px; text-align: center; }
        .sub { margin: 0 auto 22px; max-width: 420px; color: #64748b; font-size: 12.5px; line-height: 1.7; text-align: center; }

        .field { margin-bottom: 14px; }
        .row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .lbl { display: block; margin-bottom: 6px; color: #1e293b; font-size: 12px; font-weight: 600; }
        .iw { position: relative; }
        .lead { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); display: flex; color: #7a8ba5; pointer-events: none; transition: .2s; }
        .iw:focus-within .lead { color: #4F46E5; }
        .input { width: 100%; height: 48px; padding: 0 14px 0 42px; border: 1px solid #e0e7ff; border-radius: 13px; outline: none; background: #f8f7ff; color: #0f172a; font: inherit; font-size: 13px; transition: all .2s ease; }
        select.input { appearance: none; padding-right: 40px; cursor: pointer;
            background: #f8f7ff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 14px center / 16px; }
        .input::placeholder { color: #94a3b8; }
        .input:hover { border-color: #c7d2fe; }
        .input:focus { background-color: #fff; border-color: #4F46E5; box-shadow: 0 0 0 4px rgba(79,70,229,.15); }
        .input:disabled { opacity: .6; cursor: not-allowed; }
        .error { margin-top: 6px; color: #dc2626; font-size: 11px; }

        .gender { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .gender label { position: relative; cursor: pointer; }
        .gender input { position: absolute; opacity: 0; pointer-events: none; }
        .gender span { display: flex; align-items: center; justify-content: center; gap: 9px; height: 48px; border: 1.5px solid #e0e7ff; border-radius: 13px; background: #f8f7ff; color: #475569; font-size: 13px; font-weight: 500; transition: all .2s ease; }
        .gender span:hover { border-color: #c7d2fe; }
        .gender input:checked + span { color: #4F46E5; border-color: #4F46E5; background: #eef2ff; font-weight: 600; box-shadow: 0 0 0 4px rgba(79,70,229,.1); }
        .gender input:focus-visible + span { outline: 3px solid #c4b5fd; outline-offset: 2px; }

        .btn { width: 100%; height: 50px; margin-top: 8px; border: 0; border-radius: 14px; background: linear-gradient(135deg, #1E2A6B, #4F46E5); color: #fff; font: inherit; font-size: 14px; font-weight: 600; cursor: pointer; box-shadow: 0 12px 26px rgba(30,42,107,.35); transition: all .2s ease; }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 16px 32px rgba(30,42,107,.45); }
        .btn:focus-visible, .out:focus-visible { outline: 3px solid rgba(79,70,229,.45); outline-offset: 3px; }

        .note { margin: 14px 0 0; color: #94a3b8; font-size: 11px; line-height: 1.6; text-align: center; }
        .out { display: block; margin: 8px auto 0; padding: 4px 8px; border: 0; border-radius: 6px; background: none; color: #4F46E5; font: 600 11.5px 'Poppins', sans-serif; cursor: pointer; }
        .out:hover { text-decoration: underline; text-underline-offset: 3px; }

        @media (prefers-reduced-motion: reduce) { .orb, .grid, .mascot, .card { animation: none !important; } }
        @media (max-width: 520px) {
            .card { padding: 48px 20px 20px; border-radius: 22px; }
            .row2, .gender { grid-template-columns: 1fr; }
            .steps li span { display: none; }
            .steps li.now span { display: inline; }
            .mascot { width: 96px; margin-bottom: -32px; }
        }
    </style>
</head>

<body>
<x-toast />

<div class="stage">
    <div class="bg" aria-hidden="true"><i class="orb o1"></i><i class="orb o2"></i><div class="grid"></div></div>

    <main class="wrap">
        <div class="mascot"><img src="{{ asset('images/nyapa.png') }}" alt="Maskot NARA"></div>

        <section class="card">
            <ol class="steps" aria-label="Langkah pendaftaran">
                <li class="done"><b><svg class="ico" viewBox="0 0 24 24" style="width:12px;height:12px"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg></b><span>Buat akun</span></li>
                <i></i>
                <li class="now"><b>2</b><span>Lengkapi data diri</span></li>
                <i></i>
                <li><b>3</b><span>Mulai</span></li>
            </ol>

            <h1>Satu langkah lagi, {{ explode(' ', trim(auth()->user()->name))[0] }}</h1>
            <p class="sub">Lengkapi data dirimu supaya NARA bisa memberi rekomendasi yang tepat. Form ini hanya muncul sekali.</p>

            <form method="POST" action="{{ route('onboarding.store') }}" novalidate>
                @csrf

                <div class="field">
                    <label for="nim" class="lbl">NIM</label>
                    <div class="iw">
                        <span class="lead"><svg class="ico" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="3"/><circle cx="9" cy="11" r="2"/><path d="M14 10h4M14 14h3M6 16a3 3 0 0 1 6 0"/></svg></span>
                        <input id="nim" class="input" type="text" name="nim" inputmode="numeric" maxlength="30" autocomplete="off"
                               value="{{ old('nim', str_starts_with((string) $m->nim, 'TEMP-') ? '' : $m->nim) }}" placeholder="Contoh: 2403045039" required autofocus>
                    </div>
                    @error('nim')<div class="error">{{ $message }}</div>@enderror
                </div>

                <div class="row2">
                    <div class="field">
                        <label for="kampus" class="lbl">Kampus</label>
                        <div class="iw">
                            <span class="lead"><svg class="ico" viewBox="0 0 24 24"><path d="M4 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16M16 9h2a2 2 0 0 1 2 2v10M2 21h20M8 7h4M8 11h4M8 15h4"/></svg></span>
                            <select id="kampus" class="input" name="kampus_id" required>
                                <option value="">Pilih kampus</option>
                                @foreach ($kampus as $k)
                                    <option value="{{ $k->id }}" @selected((string) old('kampus_id', $m->kampus_id ?? '') === (string) $k->id)>{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('kampus_id')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="prodi" class="lbl">Program studi</label>
                        <div class="iw">
                            <span class="lead"><svg class="ico" viewBox="0 0 24 24"><path d="M12 3l9 5-9 5-9-5 9-5z"/><path d="M7 11v5c0 1.5 2.2 3 5 3s5-1.5 5-3v-5"/></svg></span>
                            <select id="prodi" class="input" name="prodi_id" required disabled>
                                <option value="">Pilih kampus dulu</option>
                            </select>
                        </div>
                        @error('prodi_id')<div class="error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="field">
                    <span class="lbl" id="jk-l">Jenis kelamin</span>
                    <div class="gender" role="radiogroup" aria-labelledby="jk-l">
                        @foreach (['Laki-laki', 'Perempuan'] as $jk)
                            <label>
                                <input type="radio" name="jenis_kelamin" value="{{ $jk }}" @checked(old('jenis_kelamin', $m->jenis_kelamin ?? '') === $jk) required>
                                <span><svg class="ico" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>{{ $jk }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('jenis_kelamin')<div class="error">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn">Simpan dan lanjut ke dashboard</button>
            </form>

            <p class="note">Data ini bisa kamu ubah lagi nanti lewat menu profil.</p>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="out">Bukan kamu? Keluar</button>
            </form>
        </section>
    </main>
</div>

<script>
    // Program studi mengikuti kampus yang dipilih
    var P = @json($prodi), sel = @json((string) old('prodi_id', $m->prodi_id ?? ''));
    var k = document.getElementById('kampus'), p = document.getElementById('prodi');

    function fill() {
        var v = k.value;
        p.innerHTML = '';
        var first = document.createElement('option');
        first.value = '';
        first.textContent = v ? 'Pilih program studi' : 'Pilih kampus dulu';
        p.appendChild(first);
        p.disabled = !v;
        P.filter(function (x) { return x.kampus_id === null || String(x.kampus_id) === v; }).forEach(function (x) {
            var o = document.createElement('option');
            o.value = x.id; o.textContent = x.nama;
            if (String(x.id) === sel) o.selected = true;
            p.appendChild(o);
        });
    }
    k.addEventListener('change', function () { sel = ''; fill(); });
    fill();
</script>

</body>
</html>