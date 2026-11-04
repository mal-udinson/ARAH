@extends('layouts.app')

@section('title', 'Monitoring Platform - Admin ARAH')

@section('content')

@php
    $ic = [
        'kampus'   => '<path d="M3 21h18"/><path d="M5 21V9l7-5 7 5v12"/><path d="M9 21v-6h6v6"/>',
        'users'    => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.5a3.5 3.5 0 0 1 0 7M18 14a6.5 6.5 0 0 1 3.5 6"/>',
        'dosen'    => '<path d="M12 3l9 4.5-9 4.5-9-4.5 9-4.5z"/><path d="M5 10.5V16c0 1.4 3.1 3 7 3s7-1.6 7-3v-5.5"/>',
        'admin'    => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
    ];

    $roleLabel = fn ($r) => match ($r) {
        'mahasiswa' => 'Mahasiswa',
        'dosen' => 'Dosen',
        'admin_kampus' => 'Admin Kampus',
        default => ucfirst($r),
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
    .aa-hero h1 { margin: 0 0 8px; font-size: clamp(26px, 4vw, 38px); line-height: 1.12; font-weight: 700; letter-spacing: -.035em; }
    .aa-hero p { margin: 0; max-width: 560px; color: rgba(255,255,255,.85); font-size: 14px; line-height: 1.7; }

    .aa-card { background: #fff; border: 1px solid rgba(255,255,255,.7); border-radius: 22px; box-shadow: 0 18px 46px rgba(8,16,70,.3); animation: aa-in .6s ease both; }
    @keyframes aa-in { from { opacity: 0; transform: translateY(16px); } }

    .aa-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-bottom: 20px; }
    .aa-stat { padding: 20px; transition: transform .25s, box-shadow .25s; }
    .aa-stat:hover { transform: translateY(-4px); box-shadow: 0 26px 56px rgba(8,16,70,.38); }
    .aa-ico { width: 46px; height: 46px; display: grid; place-items: center; border-radius: 15px; color: var(--pri); background: linear-gradient(135deg, #eef2ff, #f3e8ff); transition: transform .3s; }
    .aa-stat:hover .aa-ico { transform: rotate(-8deg) scale(1.08); }
    .aa-lb { margin-top: 14px; color: var(--mut); font-size: 12px; font-weight: 500; }
    .aa-val { margin-top: 2px; color: var(--ink); font-size: 32px; line-height: 1.2; font-weight: 700; letter-spacing: -.03em; }
    .aa-nt { margin-top: 2px; color: #94a3b8; font-size: 11px; }

    .aa-cols { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; margin-bottom: 20px; }
    .aa-pn { padding: 24px; }
    .aa-ph { display: flex; justify-content: space-between; align-items: center; gap: 15px; }
    .aa-h { margin: 0; color: var(--ink); font-size: 16px; font-weight: 700; letter-spacing: -.01em; }
    .aa-sub { margin: 4px 0 0; color: var(--mut); font-size: 12px; }
    .aa-pct { font-size: 22px; font-weight: 700; color: var(--ink); }
    .aa-bar { height: 9px; margin-top: 20px; border-radius: 999px; background: #eef2f7; overflow: hidden; }
    .aa-bar > div { height: 100%; border-radius: 999px; }
    .aa-bar--i > div { background: var(--pri); }
    .aa-bar--p > div { background: #7c3aed; }
    .aa-barcap { margin-top: 9px; color: #94a3b8; font-size: 11px; }

    .aa-ch { padding: 20px 22px; border-bottom: 1px solid var(--line); }
    .aa-tb { overflow: hidden; margin-bottom: 20px; }
    .aa-sc { overflow-x: auto; }
    .aa-table { width: 100%; min-width: 700px; border-collapse: collapse; }
    .aa-table th { padding: 14px 20px; background: var(--soft); border-bottom: 1px solid var(--line); color: var(--mut); font-size: 12px; font-weight: 600; text-align: left; }
    .aa-table td { padding: 16px 20px; border-top: 1px solid var(--line); color: var(--tx); font-size: 12.5px; vertical-align: middle; }
    .aa-table tbody tr:first-child td { border-top: 0; }
    .aa-table tbody tr { transition: background .2s; }
    .aa-table tbody tr:hover { background: #faf9ff; }
    .aa-table td.aa-empty { padding: 48px 20px; text-align: center; color: #94a3b8; font-size: 13px; }
    .aa-c { text-align: center !important; }
    .aa-r { text-align: right !important; }
    .aa-nm b { display: block; color: var(--ink); font-size: 13.5px; }
    .aa-nm small { display: block; color: #94a3b8; font-size: 11.5px; margin-top: 3px; }

    .aa-bd { display: inline-flex; align-items: center; gap: 6px; padding: 5px 9px; border-radius: 99px; font-size: 10.5px; font-weight: 600; }
    .aa-bd::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .aa-bd--on { background: #ecfdf5; color: #047857; }
    .aa-bd--off { background: #fef2f2; color: #b91c1c; }
    .aa-bd--mu { background: var(--soft); color: var(--pri); }

    @media (max-width: 1000px) { .aa-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } .aa-cols { grid-template-columns: 1fr; } }
    @media (max-width: 760px) {
        .aa { padding: 16px 12px 36px; }
        .aa-pn { padding: 18px; }
        .aa-ch { padding: 16px; }
        .aa-table thead { display: none; }
        .aa-table, .aa-table tbody, .aa-table tr { display: block; width: 100%; }
        .aa-table tr { padding: 14px 16px; border-top: 1px solid var(--line); }
        .aa-table tbody tr:first-child { border-top: 0; }
        .aa-table td { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 6px 0; border: 0; text-align: right !important; }
        .aa-table td::before { content: attr(data-label); color: #94a3b8; font-size: 11.5px; font-weight: 500; text-align: left; }
        .aa-table td.aa-first, .aa-table td.aa-empty { display: block; text-align: left !important; }
        .aa-table td.aa-empty { text-align: center !important; }
        .aa-table td.aa-first::before, .aa-table td.aa-empty::before { display: none; }
    }
    @media (max-width: 460px) { .aa-stats { gap: 9px; } .aa-stat { padding: 14px; } .aa-val { font-size: 26px; } }
    @media (prefers-reduced-motion: reduce) { .aa *, .aa *::before, .aa *::after { animation: none !important; transition: none !important; } }
</style>

<div class="aa">

    <div class="aa-bg" aria-hidden="true">
        <div class="aa-grid"></div><i class="aa-orb aa-o1"></i><i class="aa-orb aa-o2"></i>
    </div>

    <div class="aa-wrap">

        <section class="aa-hero">
            <h1>Monitoring Platform</h1>
            <p>Pantau perkembangan penggunaan ARAH di seluruh kampus.</p>
        </section>

        {{-- STATISTIK GLOBAL --}}
        <div class="aa-stats">

            <div class="aa-card aa-stat">
                <span class="aa-ico"><svg class="i" viewBox="0 0 24 24">{!! $ic['kampus'] !!}</svg></span>
                <div class="aa-lb">Kampus</div>
                <div class="aa-val">{{ $totalKampus }}</div>
                <div class="aa-nt">Institusi terhubung</div>
            </div>

            <div class="aa-card aa-stat">
                <span class="aa-ico"><svg class="i" viewBox="0 0 24 24">{!! $ic['users'] !!}</svg></span>
                <div class="aa-lb">Mahasiswa</div>
                <div class="aa-val">{{ $totalMahasiswa }}</div>
                <div class="aa-nt">Seluruh platform</div>
            </div>

            <div class="aa-card aa-stat">
                <span class="aa-ico"><svg class="i" viewBox="0 0 24 24">{!! $ic['dosen'] !!}</svg></span>
                <div class="aa-lb">Dosen</div>
                <div class="aa-val">{{ $totalDosen }}</div>
                <div class="aa-nt">Seluruh platform</div>
            </div>

            <div class="aa-card aa-stat">
                <span class="aa-ico"><svg class="i" viewBox="0 0 24 24">{!! $ic['admin'] !!}</svg></span>
                <div class="aa-lb">Admin Kampus</div>
                <div class="aa-val">{{ $totalAdminKampus }}</div>
                <div class="aa-nt">Pengelola institusi</div>
            </div>

        </div>

        {{-- PROGRESS PENGGUNAAN --}}
        <div class="aa-cols">

            <div class="aa-card aa-pn">
                <div class="aa-ph">
                    <div>
                        <h2 class="aa-h">Penyelesaian Asesmen</h2>
                        <p class="aa-sub">Mahasiswa yang telah mengisi asesmen.</p>
                    </div>
                    <div class="aa-pct">{{ $persentaseAsesmen }}%</div>
                </div>
                <div class="aa-bar aa-bar--i"><div style="width:{{ $persentaseAsesmen }}%"></div></div>
                <div class="aa-barcap">{{ $mahasiswaAsesmen }} dari {{ $totalMahasiswa }} mahasiswa</div>
            </div>

            <div class="aa-card aa-pn">
                <div class="aa-ph">
                    <div>
                        <h2 class="aa-h">Rekomendasi ARAH</h2>
                        <p class="aa-sub">Mahasiswa yang telah memiliki hasil rekomendasi.</p>
                    </div>
                    <div class="aa-pct">{{ $persentaseRekomendasi }}%</div>
                </div>
                <div class="aa-bar aa-bar--p"><div style="width:{{ $persentaseRekomendasi }}%"></div></div>
                <div class="aa-barcap">{{ $mahasiswaRekomendasi }} dari {{ $totalMahasiswa }} mahasiswa</div>
            </div>

        </div>

        {{-- STATISTIK PER KAMPUS --}}
        <div class="aa-card aa-tb">

            <div class="aa-ch">
                <h2 class="aa-h">Statistik Kampus</h2>
                <p class="aa-sub">Ringkasan jumlah pengguna pada setiap institusi.</p>
            </div>

            <div class="aa-sc">
                <table class="aa-table">

                    <thead>
                        <tr>
                            <th>Kampus</th>
                            <th class="aa-c">Mahasiswa</th>
                            <th class="aa-c">Dosen</th>
                            <th class="aa-c">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($kampusStats as $item)
                            <tr>
                                <td class="aa-first" data-label="Kampus">
                                    <div class="aa-nm">
                                        <b>{{ $item['nama'] }}</b>
                                        <small>{{ $item['kode'] }}</small>
                                    </div>
                                </td>

                                <td class="aa-c" data-label="Mahasiswa">{{ $item['mahasiswa'] }}</td>
                                <td class="aa-c" data-label="Dosen">{{ $item['dosen'] }}</td>

                                <td class="aa-c" data-label="Status">
                                    @if ($item['status'])
                                        <span class="aa-bd aa-bd--on">Aktif</span>
                                    @else
                                        <span class="aa-bd aa-bd--off">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="aa-empty" colspan="4">Belum ada data kampus.</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

        {{-- AKUN TERBARU --}}
        <div class="aa-card aa-tb">

            <div class="aa-ch">
                <h2 class="aa-h">Akun Terbaru</h2>
                <p class="aa-sub">Akun pengguna yang baru dibuat pada platform.</p>
            </div>

            <div class="aa-sc">
                <table class="aa-table">

                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Kampus</th>
                            <th class="aa-r">Dibuat</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($akunTerbaru as $akun)
                            <tr>
                                <td class="aa-first" data-label="Nama">
                                    <div class="aa-nm">
                                        <b>{{ $akun->name }}</b>
                                        <small>{{ $akun->email }}</small>
                                    </div>
                                </td>

                                <td data-label="Role"><span class="aa-bd aa-bd--mu">{{ $roleLabel($akun->role) }}</span></td>

                                <td data-label="Kampus">{{ $akun->kampus->kode ?? '-' }}</td>

                                <td class="aa-r aa-end" data-label="Dibuat">{{ $akun->created_at?->format('d M Y, H:i') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="aa-empty" colspan="4">Belum ada akun pengguna.</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

    </div>
</div>

@endsection