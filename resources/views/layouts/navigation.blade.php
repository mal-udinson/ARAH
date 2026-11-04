@php
    $u = auth()->user();
    $links = [
        ['dashboard',       'Dashboard',     'dashboard'],
        ['assessment.show', 'Asesmen',       'assessment.*'],
        ['recommendations', 'Rekomendasi',   'recommendations*'],
        ['chat',            'Tanya NARA',    'chat*'],
        ['academic.index',  'Data akademik', 'academic.*'],
    ];
@endphp

<style>
    .nv { position: sticky; top: 0; z-index: 50; height: 64px; background: rgba(255,255,255,.88); backdrop-filter: blur(14px); border-bottom: 1px solid #e5e3ff; font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; }
    .nv *, .nv *::before, .nv *::after { box-sizing: border-box; }
    .nv-in { width: min(1280px, 100%); height: 100%; margin: 0 auto; padding: 0 22px; display: flex; align-items: center; gap: 28px; }
    .nv-logo { display: flex; align-items: center; flex-shrink: 0; }
    .nv-logo img { display: block; width: auto; height: 34px; max-width: 130px; }
    .nv-links { display: flex; align-items: center; gap: 4px; flex: 1; }
    .nv-links a { padding: 8px 14px; border-radius: 10px; color: #475569; font-size: 12.5px; font-weight: 500; text-decoration: none; transition: background .2s, color .2s; }
    .nv-links a:hover { color: #4F46E5; background: #f8f7ff; }
    .nv-links a.on { color: #4F46E5; font-weight: 600; background: #eef2ff; }

    .nv-user { position: relative; margin-left: auto; }
    .nv-chip { display: flex; align-items: center; gap: 9px; padding: 5px 12px 5px 5px; border: 1px solid #e5e3ff; border-radius: 99px; background: #fff; color: #1E2A6B; font: inherit; font-size: 12px; font-weight: 600; cursor: pointer; transition: border-color .2s, box-shadow .2s; }
    .nv-chip:hover { border-color: #c7c2ff; box-shadow: 0 6px 16px rgba(79,70,229,.12); }
    .nv-av { width: 30px; height: 30px; display: grid; place-items: center; border-radius: 50%; color: #fff; font-size: 12px; font-weight: 700; background: linear-gradient(135deg, #4F46E5, #7C3AED); }
    .nv-nm { max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .nv-chip svg { width: 14px; height: 14px; fill: none; stroke: currentColor; stroke-width: 2.4; stroke-linecap: round; stroke-linejoin: round; }
    .nv-menu { position: absolute; right: 0; top: calc(100% + 10px); min-width: 190px; padding: 6px; border: 1px solid #e5e3ff; border-radius: 14px; background: #fff; box-shadow: 0 18px 40px rgba(43,47,160,.18); opacity: 0; transform: translateY(-6px); pointer-events: none; transition: opacity .2s, transform .2s; }
    .nv-menu.show { opacity: 1; transform: none; pointer-events: auto; }
    .nv-menu a, .nv-menu button { display: block; width: 100%; padding: 10px 12px; border: 0; border-radius: 9px; background: none; color: #334155; font: inherit; font-size: 12.5px; font-weight: 500; text-align: left; text-decoration: none; cursor: pointer; }
    .nv-menu a:hover { background: #f8f7ff; color: #4F46E5; }
    .nv-menu button { color: #dc2626; }
    .nv-menu button:hover { background: #fef2f2; }

    .nv-burger { display: none; width: 40px; height: 40px; margin-left: 4px; border: 1px solid #e5e3ff; border-radius: 12px; background: #fff; color: #1E2A6B; cursor: pointer; place-items: center; }
    .nv-burger svg { width: 20px; height: 20px; fill: none; stroke: currentColor; stroke-width: 2.2; stroke-linecap: round; }

    @media (max-width: 900px) {
        .nv-in { padding: 0 14px; gap: 10px; }
        .nv-burger { display: grid; }
        .nv-nm { display: none; }
        .nv-chip { padding: 5px; }
        .nv-chip svg { display: none; }
        .nv-links { position: absolute; left: 0; right: 0; top: 64px; flex-direction: column; align-items: stretch; padding: 10px 14px 14px; background: #fff; border-bottom: 1px solid #e5e3ff; box-shadow: 0 18px 30px rgba(43,47,160,.12); opacity: 0; transform: translateY(-8px); pointer-events: none; transition: opacity .2s, transform .2s; }
        .nv.open .nv-links { opacity: 1; transform: none; pointer-events: auto; }
        .nv-links a { padding: 12px 14px; font-size: 13px; }
    }
</style>

<header class="nv">
    <div class="nv-in">
        <a href="{{ route('dashboard') }}" class="nv-logo"><img src="{{ asset('images/LOGO.png') }}" alt="ARAH"></a>

        <nav class="nv-links">
            @foreach ($links as $l)
                <a href="{{ route($l[0]) }}" class="{{ request()->routeIs($l[2]) ? 'on' : '' }}">{{ $l[1] }}</a>
            @endforeach
        </nav>

        <div class="nv-user">
            <button type="button" id="nvBtn" class="nv-chip" aria-haspopup="true" aria-expanded="false">
                <span class="nv-av">{{ strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                <span class="nv-nm">{{ $u->name }}</span>
                <svg viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div class="nv-menu" id="nvMenu">
                <a href="{{ route('profile.edit') }}">Edit data diri</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Keluar</button>
                </form>
            </div>
        </div>

        <button type="button" class="nv-burger" id="nvBurger" aria-label="Buka menu">
            <svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
    </div>
</header>

<script>
    (function () {
        var h = document.querySelector('.nv'), b = document.getElementById('nvBtn'), m = document.getElementById('nvMenu');
        document.getElementById('nvBurger').addEventListener('click', function () { h.classList.toggle('open'); });
        b.addEventListener('click', function (e) {
            e.stopPropagation();
            m.classList.toggle('show');
            b.setAttribute('aria-expanded', m.classList.contains('show'));
        });
        document.addEventListener('click', function () { m.classList.remove('show'); b.setAttribute('aria-expanded', 'false'); });
    })();
</script>