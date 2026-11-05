@extends('layouts.app')

@section('title', 'Audit Log - Admin ARAH')

@section('content')

@php
    $ic = [
        'log'    => '<path d="M4 6h16M4 12h16M4 18h10"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/>',
        'reset'  => '<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/>',
    ];

    $actionLabel = fn ($a) => match ($a) {
        'create' => 'Tambah',
        'update' => 'Ubah',
        'delete' => 'Hapus',
        'status_change' => 'Status',
        default => ucfirst($a),
    };
    $actionBadge = fn ($a) => match ($a) {
        'create' => 'aa-bd--on',
        'delete' => 'aa-bd--off',
        default  => 'aa-bd--mu',
    };
@endphp

<style>
    .aa { --ink:#1E2A6B; --pri:#4F46E5; --tx:#334155; --mut:#64748b; --line:#e8e4ff; --soft:#f6f4ff;
        position: relative; overflow: hidden; min-height: calc(100vh - 70px); padding: 30px 22px 60px; color: var(--tx);
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; background: linear-gradient(165deg, #15174a 0%, #2f2a9b 50%, #8b2fc9 100%); }
    .aa *, .aa *::before, .aa *::after { box-sizing: border-box; }
    .aa-wrap { position: relative; z-index: 2; width: min(1280px, 100%); margin: 0 auto; }
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

    .aa-hero { margin-bottom: 26px; padding: 14px 8px 8px; color: #fff; }
    .aa-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .aa-chip { display: inline-flex; align-items: center; gap: 7px; padding: 6px 12px; border-radius: 99px; background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.26); font-size: 11.5px; font-weight: 600; }
    .aa-chip .i { width: 14px; height: 14px; }
    .aa-hero h1 { margin: 0 0 12px; font-size: clamp(26px, 4vw, 38px); line-height: 1.12; font-weight: 700; letter-spacing: -.035em; }
    .aa-hero p { margin: 0; max-width: 560px; color: rgba(255,255,255,.85); font-size: 14px; line-height: 1.7; }

    .aa-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 48px; padding: 0 20px; border: 0; border-radius: 14px; font-family: inherit; font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer; transition: transform .2s, box-shadow .2s; }
    .aa-btn .i { width: 17px; height: 17px; }
    .aa-btn:hover { transform: translateY(-2px); }
    .aa-btn--p { background: linear-gradient(135deg, #1E2A6B, #4F46E5); color: #fff; box-shadow: 0 12px 26px rgba(79,70,229,.35); }
    .aa-btn--g { background: var(--soft); color: var(--ink); border: 1px solid var(--line); }
    .aa-btn:focus-visible, .aa-mini:focus-visible { outline: 3px solid #a5b4fc; outline-offset: 2px; }

    .aa-card { background: #fff; border: 1px solid rgba(255,255,255,.7); border-radius: 22px; box-shadow: 0 18px 46px rgba(8,16,70,.3); animation: aa-in .6s ease both; }
    @keyframes aa-in { from { opacity: 0; transform: translateY(16px); } }

    .aa-flt { padding: 20px; margin-bottom: 18px; }
    .aa-fg { display: grid; grid-template-columns: minmax(220px, 1.6fr) minmax(140px, 1fr) minmax(140px, 1fr) minmax(140px, 1fr) auto; gap: 12px; align-items: end; }
    .aa-fb { display: flex; gap: 10px; }
    .aa-fb .aa-btn { min-height: 48px; }
    .aa-lbl { display: block; margin-bottom: 8px; color: var(--ink); font-size: 12px; font-weight: 600; }
    .aa-in { width: 100%; min-height: 48px; padding: 12px 16px; border: 1px solid var(--line); border-radius: 14px; background-color: var(--soft); color: var(--ink); font-family: inherit; font-size: 13.5px; outline: 0; transition: border-color .2s, box-shadow .2s, background-color .2s; }
    .aa-in::placeholder { color: #a3adc2; }
    .aa-in:focus { border-color: #a5b4fc; background-color: #fff; box-shadow: 0 0 0 4px rgba(79,70,229,.14); }
    .aa-sw { position: relative; }
    .aa-sw .i { position: absolute; left: 14px; top: 50%; width: 17px; height: 17px; margin-top: -8.5px; color: #94a3b8; pointer-events: none; }
    .aa-sw .aa-in { padding-left: 42px; }
    .aa-sel { -webkit-appearance: none; appearance: none; padding-right: 36px; background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); }

    .aa-ch { padding: 20px 22px; border-bottom: 1px solid var(--line); }
    .aa-h { margin: 0; color: var(--ink); font-size: 18px; font-weight: 700; letter-spacing: -.01em; }
    .aa-sub { margin: 4px 0 0; color: var(--mut); font-size: 12.5px; }

    .aa-tb { overflow: hidden; }
    .aa-sc { overflow-x: auto; }
    .aa-table { width: 100%; min-width: 950px; border-collapse: collapse; }
    .aa-table th { padding: 14px 20px; background: var(--soft); border-bottom: 1px solid var(--line); color: var(--mut); font-size: 12px; font-weight: 600; text-align: left; white-space: nowrap; }
    .aa-table td { padding: 16px 20px; border-top: 1px solid var(--line); color: var(--tx); font-size: 12.5px; vertical-align: top; }
    .aa-table tbody tr:first-child td { border-top: 0; }
    .aa-table tbody tr { transition: background .2s; }
    .aa-table tbody tr:hover { background: #faf9ff; }
    .aa-table td.aa-empty { padding: 48px 20px; text-align: center; color: #94a3b8; font-size: 13px; }
    .aa-nm b { display: block; color: var(--ink); font-size: 13.5px; }
    .aa-nm small { display: block; color: #94a3b8; font-size: 11.5px; margin-top: 2px; }
    .aa-rt { margin-top: 4px; color: #9ca3af; font-size: 11px; }

    .aa-bd { display: inline-flex; align-items: center; gap: 6px; padding: 5px 11px; border-radius: 99px; font-size: 11px; font-weight: 600; white-space: nowrap; }
    .aa-bd::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .aa-bd--on { background: #ecfdf5; color: #047857; }
    .aa-bd--off { background: #fef2f2; color: #b91c1c; }
    .aa-bd--mu { background: var(--soft); color: var(--pri); }

    .aa-pg-wrap { padding: 16px 22px; border-top: 1px solid var(--line); }
    .aa-pg-wrap nav { font-family: inherit; }

    @media (max-width: 1000px) {
        .aa-fg { grid-template-columns: 1fr 1fr; }
        .aa-fg > div:first-child { grid-column: 1 / -1; }
    }
    @media (max-width: 760px) {
        .aa { padding: 16px 12px 36px; }
        .aa-fg { grid-template-columns: 1fr; }
        .aa-fb .aa-btn { flex: 1; }
        .aa-ch { padding: 16px; }
        .aa-table thead { display: none; }
        .aa-table, .aa-table tbody, .aa-table tr { display: block; width: 100%; }
        .aa-table tr { padding: 14px 16px; border-top: 1px solid var(--line); }
        .aa-table tbody tr:first-child { border-top: 0; }
        .aa-table td { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; padding: 6px 0; border: 0; text-align: right !important; }
        .aa-table td::before { content: attr(data-label); color: #94a3b8; font-size: 11.5px; font-weight: 500; text-align: left; flex-shrink: 0; }
        .aa-table td.aa-first, .aa-table td.aa-empty { display: block; text-align: left !important; }
        .aa-table td.aa-empty { text-align: center !important; }
        .aa-table td.aa-first::before, .aa-table td.aa-empty::before { display: none; }
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
                <span class="aa-chip"><svg class="i" viewBox="0 0 24 24">{!! $ic['log'] !!}</svg>{{ number_format($logs->total()) }} aktivitas tercatat</span>
            </div>
            <h1>Audit Log</h1>
            <p>Riwayat aktivitas administratif pada platform ARAH.</p>
        </section>

        {{-- FILTER --}}
        <form class="aa-card aa-flt" method="GET">
            <div class="aa-fg">

                <div>
                    <label class="aa-lbl" for="search">Cari</label>
                    <div class="aa-sw">
                        <svg class="i" viewBox="0 0 24 24">{!! $ic['search'] !!}</svg>
                        <input class="aa-in" id="search" type="text" name="search" value="{{ $search }}" placeholder="Nama, email, aktivitas...">
                    </div>
                </div>

                <div>
                    <label class="aa-lbl" for="role">Role</label>
                    <select class="aa-in aa-sel" id="role" name="role">
                        <option value="">Semua Role</option>
                        <option value="admin_arah" @selected($role === 'admin_arah')>Admin ARAH</option>
                        <option value="admin_kampus" @selected($role === 'admin_kampus')>Admin Kampus</option>
                    </select>
                </div>

                <div>
                    <label class="aa-lbl" for="action">Aksi</label>
                    <select class="aa-in aa-sel" id="action" name="action">
                        <option value="">Semua Aksi</option>
                        <option value="create" @selected($action === 'create')>Tambah</option>
                        <option value="update" @selected($action === 'update')>Ubah</option>
                        <option value="delete" @selected($action === 'delete')>Hapus</option>
                        <option value="status_change" @selected($action === 'status_change')>Status</option>
                    </select>
                </div>

                <div>
                    <label class="aa-lbl" for="module">Modul</label>
                    <select class="aa-in aa-sel" id="module" name="module">
                        <option value="">Semua Modul</option>
                        @foreach ($modules as $item)
                            <option value="{{ $item }}" @selected($module === $item)>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="aa-fb">
                    <button type="submit" class="aa-btn aa-btn--p">
                        <svg class="i" viewBox="0 0 24 24">{!! $ic['search'] !!}</svg>Filter
                    </button>
                    <a class="aa-btn aa-btn--g" href="{{ url()->current() }}">
                        <svg class="i" viewBox="0 0 24 24">{!! $ic['reset'] !!}</svg>Reset
                    </a>
                </div>

            </div>
        </form>

        {{-- HASIL --}}
        <div class="aa-card aa-tb">

            <div class="aa-ch">
                <h2 class="aa-h">Riwayat Aktivitas</h2>
                <p class="aa-sub">Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} aktivitas</p>
            </div>

            <div class="aa-sc">
                <table class="aa-table">

                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Role</th>
                            <th>Aksi</th>
                            <th>Modul</th>
                            <th>Aktivitas</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td class="aa-first" data-label="Waktu">{{ $log->created_at->format('d M Y H:i') }}</td>

                                <td data-label="Pengguna">
                                    <div class="aa-nm">
                                        <b>{{ $log->user?->name ?? 'User terhapus' }}</b>
                                        @if ($log->user?->email)
                                            <small>{{ $log->user->email }}</small>
                                        @endif
                                    </div>
                                </td>

                                <td data-label="Role">{{ ['admin_arah' => 'Admin ARAH', 'admin_kampus' => 'Admin Kampus', 'dosen' => 'Dosen PA', 'mahasiswa' => 'Mahasiswa'][$log->role] ?? ucfirst((string) $log->role) }}</td>

                                <td data-label="Aksi">
                                    <span class="aa-bd {{ $actionBadge($log->action) }}">{{ $actionLabel($log->action) }}</span>
                                </td>

                                <td data-label="Modul">{{ $log->module }}</td>

                                <td data-label="Aktivitas">
                                    {{ $log->description }}
                                    @if ($log->route_name)
                                        <div class="aa-rt">{{ $log->route_name }}</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="aa-empty" colspan="6">Belum ada aktivitas yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            @if ($logs->hasPages())
                <div class="aa-pg-wrap">
                    {{ $logs->links() }}
                </div>
            @endif

        </div>

    </div>
</div>

@endsection