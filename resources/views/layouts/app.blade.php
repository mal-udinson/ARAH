@php
$u = auth()->user();

$unreadDosenChat = 0;

if ($u->role === 'mahasiswa') {

    $mahasiswaChat = \App\Models\Mahasiswa::where(
        'user_id',
        $u->id
    )->first();

    if ($mahasiswaChat) {
        $unreadDosenChat = \App\Models\DosenChatMessage::where(
            'mahasiswa_id',
            $mahasiswaChat->id
        )
        ->where('sender_user_id', '!=', $u->id)
        ->whereNull('read_at')
        ->count();
    }

} elseif ($u->role === 'dosen') {

    $dosenChat = \App\Models\Dosen::where(
        'user_id',
        $u->id
    )->first();

    if ($dosenChat) {
        $unreadDosenChat = \App\Models\DosenChatMessage::where(
            'dosen_id',
            $dosenChat->id
        )
        ->where('sender_user_id', '!=', $u->id)
        ->whereNull('read_at')
        ->count();
    }
}

if ($u->role === 'dosen') {

    $menu = [
        ['dosen.dashboard', 'Dashboard', 'dosen.dashboard'],
        ['dosen.chat.index', 'Chat Mahasiswa', 'dosen.chat.*'],
    ];

} elseif ($u->role === 'admin_kampus') {

    $menu = [
        ['admin-kampus.dashboard', 'Dashboard', 'admin-kampus.dashboard'],
        ['admin-kampus.mahasiswa.index', 'Mahasiswa', 'admin-kampus.mahasiswa.*'],
        ['admin-kampus.dosen.index', 'Dosen', 'admin-kampus.dosen.*'],
    ];

} elseif ($u->role === 'admin_arah') {

    $menu = [
        ['admin-arah.dashboard', 'Dashboard', 'admin-arah.dashboard'],
        ['admin-arah.kampus.index', 'Kampus', 'admin-arah.kampus.*'],
        ['admin-arah.mahasiswa.index', 'Mahasiswa', 'admin-arah.mahasiswa.*'],
        ['admin-arah.dosen.index', 'Dosen', 'admin-arah.dosen.*'],
        ['admin-arah.admin-kampus.index', 'Admin Kampus', 'admin-arah.admin-kampus.*'],
        ['admin-arah.monitoring.index', 'Monitoring', 'admin-arah.monitoring.*'],
        ['admin-arah.audit-log.index', 'Audit Log', 'admin-arah.audit-log.*'],
    ];

} else {

    $menu = [
        ['dashboard', 'Dashboard', 'dashboard'],
        ['assessment.show', 'Asesmen', 'assessment.*'],
        ['interest.profile', 'Minat', 'interest.*'],
        ['recommendations', 'Rekomendasi', 'recommendations*'],
        ['chat', 'Tanya NARA', 'chat*'],
        ['nara.voice', 'Voice NARA', 'nara.voice'],
        ['chat-dosen', 'Chat Dosen', 'chat-dosen*'],
        ['academic.index', 'Data akademik', 'academic.*'],
    ];
}

@endphp

<!DOCTYPE html>

<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ARAH')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

<style>

body {
    margin: 0;
    background:
        linear-gradient(
            180deg,
            #f5f3ff,
            #eef4ff
        ) fixed;
    color: #334155;
    font-family:
        'Poppins',
        ui-sans-serif,
        system-ui,
        sans-serif;
}

.nv,
.nv *,
.nv *::before,
.nv *::after {
    box-sizing: border-box;
}

.nv {
    position: sticky;
    top: 0;
    z-index: 50;
    height: 64px;

    background:
        linear-gradient(
            90deg,
            #1E2A6B,
            #2B2FA0 65%,
            #3a2ea6
        );

    border-bottom:
        1px solid rgba(255,255,255,.1);

    box-shadow:
        0 8px 24px rgba(15,20,80,.22);
}

.nv-in {
    width: min(1280px, 100%);
    height: 100%;
    margin: 0 auto;
    padding: 0 22px;

    display: flex;
    align-items: center;

    gap: 32px;
}

.nv-logo {
    display: flex;
    flex-shrink: 0;
}

.nv-logo img {
    display: block;
    width: auto;
    height: 32px;
    max-width: 130px;

    filter:
        brightness(0)
        invert(1);
}

.nv-links {
    display: flex;
    align-items: stretch;
    align-self: stretch;

    gap: 2px;
    flex: 1;
}

.nv-links a {
    position: relative;

    display: flex;
    align-items: center;

    padding: 0 16px;

    color:
        rgba(255,255,255,.72);

    font-size: 12.5px;
    font-weight: 500;

    text-decoration: none;

    transition:
        color .2s,
        background .2s;
}

.nv-links a:hover {
    color: #fff;

    background:
        rgba(255,255,255,.07);
}

.nv-links a.on {
    color: #fff;
    font-weight: 600;
}

.nv-links a.on::after {
    content: "";

    position: absolute;

    left: 16px;
    right: 16px;
    bottom: 0;

    height: 3px;

    border-radius:
        3px 3px 0 0;

    background: #00B7F4;
}

.nv a:focus-visible,
.nv button:focus-visible {
    outline:
        2px solid #00B7F4;

    outline-offset: -2px;
}

.nv-user {
    position: relative;
    margin-left: auto;
}

.nv-chip {
    display: flex;
    align-items: center;

    gap: 9px;

    padding:
        5px
        12px
        5px
        5px;

    border:
        1px solid rgba(255,255,255,.22);

    border-radius: 99px;

    background:
        rgba(255,255,255,.08);

    color: #fff;

    font: inherit;
    font-size: 12px;
    font-weight: 600;

    cursor: pointer;

    transition:
        background .2s;
}

.nv-chip:hover {
    background:
        rgba(255,255,255,.15);
}

.nv-av {
    width: 30px;
    height: 30px;

    display: grid;
    place-items: center;

    border-radius: 50%;

    background:
        linear-gradient(
            135deg,
            #4F46E5,
            #7C3AED
        );

    font-size: 12px;
    font-weight: 700;
}

.nv-nm {
    max-width: 130px;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.nv-chip svg,
.nv-burger svg {
    fill: none;
    stroke:
        currentColor;

    stroke-linecap:
        round;

    stroke-linejoin:
        round;
}

.nv-chip svg {
    width: 14px;
    height: 14px;

    stroke-width: 2.4;
}

.nv-menu {
    position: absolute;

    right: 0;
    top:
        calc(100% + 10px);

    min-width: 190px;

    padding: 6px;

    border:
        1px solid #e5e3ff;

    border-radius: 14px;

    background: #fff;

    box-shadow:
        0 18px 40px rgba(15,20,80,.25);

    opacity: 0;

    transform:
        translateY(-6px);

    pointer-events: none;

    transition:
        opacity .2s,
        transform .2s;
}

.nv-menu.show {
    opacity: 1;

    transform: none;

    pointer-events: auto;
}

.nv-menu a,
.nv-menu button {
    display: block;

    width: 100%;

    padding:
        10px
        12px;

    border: 0;

    border-radius: 9px;

    background: none;

    color: #334155;

    font: inherit;

    font-size: 12.5px;
    font-weight: 500;

    text-align: left;

    text-decoration: none;

    cursor: pointer;
}

.nv-menu a:hover {
    background:
        #f8f7ff;

    color:
        #4F46E5;
}

.nv-menu button {
    color:
        #dc2626;
}

.nv-menu button:hover {
    background:
        #fef2f2;
}

.nv-burger {
    display: none;

    width: 40px;
    height: 40px;

    place-items: center;

    border:
        1px solid rgba(255,255,255,.22);

    border-radius: 12px;

    background: transparent;

    color: #fff;

    cursor: pointer;
}

.nv-burger svg {
    width: 20px;
    height: 20px;

    stroke-width: 2.2;
}


@media (max-width: 900px) {

    .nv-in {
        padding: 0 14px;
        gap: 10px;
    }

    .nv-burger {
        display: grid;
    }

    .nv-nm,
    .nv-chip svg {
        display: none;
    }

    .nv-chip {
        padding: 5px;
    }

    .nv-links {
        position: absolute;

        left: 0;
        right: 0;
        top: 64px;

        flex-direction: column;

        align-self: auto;

        padding:
            8px
            14px
            14px;

        background:
            #1E2A6B;

        box-shadow:
            0 18px 30px rgba(15,20,80,.3);

        opacity: 0;

        transform:
            translateY(-8px);

        pointer-events: none;

        transition:
            opacity .2s,
            transform .2s;
    }

    .nv.open .nv-links {
        opacity: 1;

        transform: none;

        pointer-events: auto;
    }

    .nv-links a {
        padding:
            13px
            14px;

        border-radius: 10px;

        font-size: 13px;
    }

    .nv-links a.on {
        background:
            rgba(255,255,255,.12);
    }

    .nv-links a.on::after {
        display: none;
    }

}

@media (prefers-reduced-motion: reduce) {

    .nv *,
    .nv-menu {
        transition: none !important;
    }

}

</style>

@stack('styles')

</head>

<body>

<x-toast />

<header class="nv">

    <div class="nv-in">

        <a
            href="
                {{
                    $u->role === 'dosen'
                        ? route('dosen.dashboard')
                        : (
                            $u->role === 'admin_kampus'
                                ? route('admin-kampus.dashboard')
                                : (
                                    $u->role === 'admin_arah'
                                        ? route('admin-arah.dashboard')
                                        : route('dashboard')
                                )
                        )
                }}
            "
            class="nv-logo"
        >
            <img
                src="{{ asset('images/LOGO.png') }}"
                alt="ARAH"
            >
        </a>


        <nav
            class="nv-links"
            aria-label="Menu utama"
        >

            @foreach ($menu as $l)

                @php

                    if ($l[0] === 'chat') {

                        $active =
                            request()->routeIs('chat') ||
                            request()->routeIs('chat.show') ||
                            request()->routeIs('chat.rename') ||
                            request()->routeIs('chat.destroy');

                    } elseif ($l[0] === 'chat-dosen') {

                        $active =
                            request()->routeIs('chat-dosen*');

                    } else {

                        $active =
                            request()->routeIs($l[2]);
                    }

                @endphp


                <a
                    href="{{ route($l[0]) }}"
                    class="{{ $active ? 'on' : '' }}"
                    @if ($active)
                        aria-current="page"
                    @endif
                >

                    {{ $l[1] }}


                    @if (
                        (
                            $l[0] === 'chat-dosen' ||
                            $l[0] === 'dosen.chat.index'
                        )
                        &&
                        $unreadDosenChat > 0
                    )

                        <span style="
                            display:inline-flex;
                            align-items:center;
                            justify-content:center;

                            min-width:18px;
                            height:18px;

                            margin-left:6px;
                            padding:0 5px;

                            border-radius:999px;

                            background:#ef4444;

                            color:#fff;

                            font-size:9px;
                            font-weight:700;
                            line-height:1;
                        ">
                            {{
                                $unreadDosenChat > 99
                                    ? '99+'
                                    : $unreadDosenChat
                            }}
                        </span>

                    @endif

                </a>

            @endforeach

        </nav>


        <div class="nv-user">

            <button
                type="button"
                id="nvBtn"
                class="nv-chip"
                aria-haspopup="true"
                aria-expanded="false"
            >

                <span class="nv-av">
                    {{
                        strtoupper(
                            mb_substr(
                                $u->name,
                                0,
                                1
                            )
                        )
                    }}
                </span>

                <span class="nv-nm">
                    {{ $u->name }}
                </span>

                <svg viewBox="0 0 24 24">
                    <path d="M6 9l6 6 6-6"/>
                </svg>

            </button>


            <div
                class="nv-menu"
                id="nvMenu"
            >

                @if ($u->role === 'mahasiswa')

                    <a
                        href="{{ route('profile.edit') }}"
                    >
                        Edit data diri
                    </a>

                @endif


                <form
                    method="POST"
                    action="{{ route('logout') }}"
                >

                    @csrf

                    <button type="submit">
                        Keluar
                    </button>

                </form>

            </div>

        </div>


        <button
            type="button"
            class="nv-burger"
            id="nvBurger"
            aria-label="Buka menu"
        >

            <svg viewBox="0 0 24 24">
                <path d="M4 7h16M4 12h16M4 17h16"/>
            </svg>

        </button>

    </div>

</header>


<main>
    @yield('content')
</main>


<script>

(function () {

    var h =
        document.querySelector('.nv'),

        b =
        document.getElementById('nvBtn'),

        m =
        document.getElementById('nvMenu');


    function close() {

        m.classList.remove('show');

        b.setAttribute(
            'aria-expanded',
            'false'
        );
    }


    document
        .getElementById('nvBurger')
        .addEventListener(
            'click',
            function (e) {

                e.stopPropagation();

                h.classList.toggle('open');

            }
        );


    b.addEventListener(
        'click',
        function (e) {

            e.stopPropagation();

            m.classList.toggle('show');

            b.setAttribute(
                'aria-expanded',
                m.classList.contains('show')
            );

        }
    );


    document.addEventListener(
        'click',
        function () {

            close();

            h.classList.remove(
                'open'
            );

        }
    );


    document.addEventListener(
        'keydown',
        function (e) {

            if (e.key === 'Escape') {

                close();

                h.classList.remove(
                    'open'
                );
            }

        }
    );

})();

</script>

@stack('scripts')

</body>
</html>