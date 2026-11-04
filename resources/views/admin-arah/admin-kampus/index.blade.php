@extends('layouts.app')

@section('title', 'Admin Kampus - Admin ARAH')

@section('content')

@php
    $ic = [
        'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.5a3.5 3.5 0 0 1 0 7M18 14a6.5 6.5 0 0 1 3.5 6"/>',
        'plus'  => '<path d="M12 5v14M5 12h14"/>',
        'edit'  => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>',
        'trash' => '<path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>',
        'power' => '<path d="M12 2v10"/><path d="M18.4 6.6a9 9 0 1 1-12.8 0"/>',
    ];
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

    .aa-hero { display: grid; grid-template-columns: 1fr auto; align-items: center; gap: 24px; margin-bottom: 26px; padding: 14px 8px 8px; color: #fff; }
    .aa-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .aa-chip { display: inline-flex; align-items: center; gap: 7px; padding: 6px 12px; border-radius: 99px; background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.26); font-size: 11.5px; font-weight: 600; }
    .aa-chip .i { width: 14px; height: 14px; }
    .aa-hero h1 { margin: 0 0 12px; font-size: clamp(26px, 4vw, 38px); line-height: 1.12; font-weight: 700; letter-spacing: -.035em; }
    .aa-hero p { margin: 0; max-width: 560px; color: rgba(255,255,255,.85); font-size: 14px; line-height: 1.7; }

    .aa-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 44px; padding: 0 20px; border: 0; border-radius: 14px; font-family: inherit; font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer; transition: transform .2s, box-shadow .2s; }
    .aa-btn .i { width: 17px; height: 17px; }
    .aa-btn:hover { transform: translateY(-2px); }
    .aa-btn--w { background: #fff; color: var(--pri); box-shadow: 0 12px 26px rgba(8,16,70,.35); }
    .aa-btn:focus-visible, .aa-mini:focus-visible { outline: 3px solid #a5b4fc; outline-offset: 2px; }

    .aa-card { background: #fff; border: 1px solid rgba(255,255,255,.7); border-radius: 22px; box-shadow: 0 18px 46px rgba(8,16,70,.3); animation: aa-in .6s ease both; }
    @keyframes aa-in { from { opacity: 0; transform: translateY(16px); } }

    .aa-ch { display: flex; justify-content: space-between; align-items: center; gap: 15px; padding: 20px 22px; border-bottom: 1px solid var(--line); }
    .aa-h { margin: 0; color: var(--ink); font-size: 18px; font-weight: 700; letter-spacing: -.01em; }
    .aa-sub { margin: 4px 0 0; color: var(--mut); font-size: 12.5px; }

    .aa-tb { overflow: hidden; }
    .aa-sc { overflow-x: auto; }
    .aa-table { width: 100%; min-width: 900px; border-collapse: collapse; }
    .aa-table th { padding: 14px 20px; background: var(--soft); border-bottom: 1px solid var(--line); color: var(--mut); font-size: 12px; font-weight: 600; text-align: left; }
    .aa-table td { padding: 16px 20px; border-top: 1px solid var(--line); color: var(--tx); font-size: 12.5px; vertical-align: middle; }
    .aa-table tbody tr:first-child td { border-top: 0; }
    .aa-table tbody tr { transition: background .2s; }
    .aa-table tbody tr:hover { background: #faf9ff; }
    .aa-table td.aa-empty { padding: 48px 20px; text-align: center; color: #94a3b8; font-size: 13px; }
    .aa-c { text-align: center !important; }
    .aa-r { text-align: right !important; }
    .aa-nm b { display: block; color: var(--ink); font-size: 13.5px; }
    .aa-nm small { color: #94a3b8; font-size: 11.5px; }

    .aa-bd { display: inline-flex; align-items: center; gap: 6px; padding: 5px 11px; border-radius: 99px; font-size: 11px; font-weight: 600; }
    .aa-bd::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .aa-bd--on { background: #ecfdf5; color: #047857; }
    .aa-bd--off { background: #fef2f2; color: #b91c1c; }

    .aa-acs { display: inline-flex; align-items: center; gap: 8px; }
    .aa-mini { display: inline-flex; align-items: center; gap: 6px; min-height: 36px; padding: 0 12px; border: 1px solid var(--line); border-radius: 11px; background: #fff; color: var(--ink); font-family: inherit; font-size: 11.5px; font-weight: 600; text-decoration: none; cursor: pointer; transition: background .2s, border-color .2s; }
    .aa-mini .i { width: 15px; height: 15px; }
    .aa-mini:hover { background: var(--soft); border-color: #c7c2ff; }
    .aa-mini--r { background: #fff5f5; border-color: #fecaca; color: #dc2626; }
    .aa-mini--g { background: #f0fdf8; border-color: #a7f3d0; color: #059669; }
    .aa-mini--p { padding: 0; }

    @media (max-width: 760px) {
        .aa { padding: 16px 12px 36px; }
        .aa-hero { grid-template-columns: 1fr; padding: 6px 0 0; }
        .aa-ch { padding: 16px; flex-direction: column; align-items: flex-start; }
        .aa-ch .aa-btn { width: 100%; justify-content: center; }
        .aa-table thead { display: none; }
        .aa-table, .aa-table tbody, .aa-table tr { display: block; width: 100%; }
        .aa-table tr { padding: 14px 16px; border-top: 1px solid var(--line); }
        .aa-table tbody tr:first-child { border-top: 0; }
        .aa-table td { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 6px 0; border: 0; text-align: right !important; }
        .aa-table td::before { content: attr(data-label); color: #94a3b8; font-size: 11.5px; font-weight: 500; text-align: left; }
        .aa-table td.aa-first, .aa-table td.aa-empty { display: block; text-align: left !important; }
        .aa-table td.aa-empty { text-align: center !important; }
        .aa-table td.aa-first::before, .aa-table td.aa-empty::before { display: none; }
        .aa-acs { flex-wrap: wrap; justify-content: flex-end; }
    }
    @media (prefers-reduced-motion: reduce) { .aa *, .aa *::before, .aa *::after { animation: none !important; transition: none !important; } }
</style>

<div class="aa">

    <div class="aa-bg" aria-hidden="true">
        <div class="aa-grid"></div><i class="aa-orb aa-o1"></i><i class="aa-orb aa-o2"></i>
    </div>

    <div class="aa-wrap">

        <section class="aa-hero">
            <div>
                <div class="aa-chips">
                    <span class="aa-chip"><svg class="i" viewBox="0 0 24 24">{!! $ic['users'] !!}</svg>{{ number_format($adminKampus->count()) }} admin kampus</span>
                </div>
                <h1>Admin Kampus</h1>
                <p>Kelola administrator yang bertanggung jawab pada setiap kampus.</p>
            </div>
        </section>

        <div class="aa-card aa-tb">

            <div class="aa-ch">
                <div>
                    <h2 class="aa-h">Daftar Admin Kampus</h2>
                    <p class="aa-sub">Semua administrator yang terdaftar di seluruh kampus.</p>
                </div>

                <a class="aa-btn aa-btn--w" href="{{ route('admin-arah.admin-kampus.create') }}">
                    <svg class="i" viewBox="0 0 24 24">{!! $ic['plus'] !!}</svg>Tambah Admin
                </a>
            </div>

            <div class="aa-sc">
                <table class="aa-table">

                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Kampus</th>
                            <th class="aa-c">Status</th>
                            <th class="aa-r">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($adminKampus as $admin)
                            <tr>
                                <td class="aa-first" data-label="Nama">
                                    <div class="aa-nm"><b>{{ $admin->name }}</b></div>
                                </td>

                                <td data-label="Email">{{ $admin->email }}</td>

                                <td data-label="Kampus">{{ $admin->kampus->nama ?? 'Belum ditentukan' }}</td>

                                <td class="aa-c" data-label="Status">
                                    @if ($admin->is_active)
                                        <span class="aa-bd aa-bd--on">Aktif</span>
                                    @else
                                        <span class="aa-bd aa-bd--off">Nonaktif</span>
                                    @endif
                                </td>

                                <td class="aa-r aa-end" data-label="Aksi">
                                    <div class="aa-acs">

                                        <form method="POST" action="{{ route('admin-arah.admin-kampus.toggle-status', $admin->id) }}"
                                              onsubmit="return confirm('Ubah status akun Admin Kampus ini?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="aa-mini {{ $admin->is_active ? 'aa-mini--r' : 'aa-mini--g' }}">
                                                <svg class="i" viewBox="0 0 24 24">{!! $ic['power'] !!}</svg>
                                                {{ $admin->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>

                                        <a class="aa-mini" href="{{ route('admin-arah.admin-kampus.edit', $admin->id) }}">
                                            <svg class="i" viewBox="0 0 24 24">{!! $ic['edit'] !!}</svg>Edit
                                        </a>

                                        <form method="POST" action="{{ route('admin-arah.admin-kampus.destroy', $admin->id) }}"
                                              onsubmit="return confirm('Hapus Admin Kampus ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="aa-mini aa-mini--r">
                                                <svg class="i" viewBox="0 0 24 24">{!! $ic['trash'] !!}</svg>Hapus
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="aa-empty" colspan="5">Belum ada Admin Kampus.</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

    </div>
</div>

@endsection