@extends('layouts.app')

@section('title', 'Data Dosen - ARAH')

@section('content')

@php
    $daftar = collect($dosen instanceof \Illuminate\Contracts\Pagination\Paginator ? $dosen->items() : $dosen)->values();
    $total  = method_exists($dosen, 'total') ? $dosen->total() : $daftar->count();

    // Pakai mahasiswa_count kalau controller sudah withCount('mahasiswa'); kalau belum, hitung per baris.
    $jumlah   = $daftar->map(fn ($d) => array_key_exists('mahasiswa_count', $d->getAttributes()) ? (int) $d->mahasiswa_count : $d->mahasiswa()->count());
    $totalMhs = $jumlah->sum();
    $rata     = $daftar->count() ? round($totalMhs / $daftar->count(), 1) : null;

    $ic = [
        'plus'   => '<path d="M12 5v14M5 12h14"/>',
        'users'  => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.5a3.5 3.5 0 0 1 0 7M18 14a6.5 6.5 0 0 1 3.5 6"/>',
        'edit'   => '<path d="M4 20h4L19 9l-4-4L4 16v4zM13 7l4 4"/>',
        'trash'  => '<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/>',
    ];
@endphp

<style>
    .dk { --ink:#1E2A6B; --pri:#4F46E5; --vio:#7C3AED; --tx:#334155; --mut:#64748b; --line:#e5e3ff;
        position: relative; overflow: hidden; min-height: calc(100vh - 64px); padding: 32px 22px 56px; color: #fff;
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; background: linear-gradient(165deg, #1b1760 0%, #3a2ea6 50%, #6b35e0 100%); }
    .dk *, .dk *::before, .dk *::after { box-sizing: border-box; }
    :where(.dk) h1, :where(.dk) h2, :where(.dk) p { margin: 0; }
    .dk-bg { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
    .dk-o1, .dk-o2 { position: absolute; border-radius: 50%; filter: blur(80px); animation: dk-dr 28s ease-in-out infinite alternate; }
    .dk-o1 { width: 460px; height: 460px; top: -150px; left: -120px; background: #8b5cf6; opacity: .5; }
    .dk-o2 { width: 400px; height: 400px; bottom: -140px; right: -120px; background: #c026d3; opacity: .26; animation-duration: 32s; }
    @keyframes dk-dr { to { transform: translate(100px, 70px) scale(1.1); } }
    .dk-gridbg { position: absolute; inset: 0; background-size: 56px 56px; -webkit-mask-image: linear-gradient(#000 20%, transparent 95%); mask-image: linear-gradient(#000 20%, transparent 95%);
        background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px); }
    .dk-wrap { position: relative; z-index: 1; width: min(1200px, 100%); margin: 0 auto; }
    .dk svg.i { width: 18px; height: 18px; flex-shrink: 0; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
    .dk-fi { animation: dk-fi .5s ease calc(var(--i, 0) * 70ms) backwards; }
    @keyframes dk-fi { from { opacity: 0; transform: translateY(14px); } }

    /* Hero transparan */
    .dk-hero { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 26px; }
    .dk-hero h1 { font-size: clamp(28px, 4.2vw, 42px); line-height: 1.15; font-weight: 700; letter-spacing: -.03em; }
    .dk-hero p { max-width: 520px; margin-top: 8px; color: rgba(255,255,255,.82); font-size: 14px; line-height: 1.75; }
    .dk-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 44px; padding: 0 20px; border-radius: 13px; color: var(--ink); background: #fff; box-shadow: 0 10px 24px rgba(10,6,60,.3); font-size: 12.5px; font-weight: 600; text-decoration: none; white-space: nowrap; transition: transform .2s; }
    .dk-btn:hover { transform: translateY(-2px); }
    .dk-btn svg.i { width: 16px; height: 16px; }
    .dk a:focus-visible, .dk button:focus-visible, .dk input:focus-visible { outline: 3px solid #c4b5fd; outline-offset: 2px; }

    /* Ringkasan */
    .dk-sum { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; margin-bottom: 26px; padding-top: 22px; border-top: 1px solid rgba(255,255,255,.2); }
    .dk-sum div { padding-left: 16px; border-left: 1px solid rgba(255,255,255,.2); }
    .dk-sum div:first-child { padding-left: 0; border-left: 0; }
    .dk-sum small { display: block; color: rgba(255,255,255,.7); font-size: 12px; }
    .dk-sum b { display: block; margin-top: 4px; font-size: 24px; font-weight: 700; line-height: 1.25; letter-spacing: -.02em; }

    /* Kartu tabel */
    .dk-card { overflow: hidden; color: var(--tx); background: #fff; border-radius: 22px; box-shadow: 0 18px 46px rgba(10,6,60,.28); }
    .dk-ch { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; padding: 20px 24px; }
    .dk-ch h2 { color: var(--ink); font-size: 17px; font-weight: 700; letter-spacing: -.01em; }
    .dk-ch small { display: block; margin-top: 3px; color: var(--mut); font-size: 12px; }
    .dk-s { position: relative; width: min(280px, 100%); }
    .dk-s svg.i { position: absolute; left: 13px; top: 50%; width: 16px; height: 16px; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
    .dk-s input { width: 100%; height: 42px; padding: 0 14px 0 38px; border: 1px solid var(--line); border-radius: 12px; outline: 0; background: #faf9ff; color: var(--tx); font: 13px 'Poppins', sans-serif; transition: border-color .2s, box-shadow .2s, background .2s; }
    .dk-s input:focus { background: #fff; border-color: #a5b4fc; box-shadow: 0 0 0 4px rgba(79,70,229,.1); }
    .dk-scroll { overflow-x: auto; }
    .dk table { width: 100%; border-collapse: collapse; }
    .dk th { padding: 12px 24px; background: #f8f7ff; color: var(--mut); font-size: 12px; font-weight: 600; text-align: left; white-space: nowrap; }
    .dk th:last-child, .dk td:last-child { text-align: right; }
    .dk td { padding: 14px 24px; border-top: 1px solid #f0eefb; font-size: 12.5px; color: #475569; vertical-align: middle; }
    .dk tbody tr { transition: background .2s; animation: dk-fi .4s ease calc(var(--i, 0) * 45ms) backwards; }
    .dk tbody tr:hover { background: #faf8ff; }
    .dk tr[hidden] { display: none; }
    .dk-p { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .dk-av { width: 40px; height: 40px; flex-shrink: 0; display: grid; place-items: center; border-radius: 13px; color: #fff; background: linear-gradient(135deg, var(--pri), var(--vio)); font-size: 15px; font-weight: 700; box-shadow: 0 8px 18px rgba(79,70,229,.25); }
    .dk-n { color: var(--ink); font-size: 13.5px; font-weight: 600; overflow-wrap: anywhere; }
    .dk-e { margin-top: 2px; color: var(--mut); font-size: 12px; overflow-wrap: anywhere; }
    .dk-chip { display: inline-flex; align-items: center; gap: 6px; padding: 4px 11px; border-radius: 99px; background: #eef2ff; color: var(--pri); font-size: 11.5px; font-weight: 600; }
    .dk-chip.z { background: #f1f5f9; color: #64748b; }
    .dk-chip svg.i { width: 14px; height: 14px; }
    .dk-act { display: inline-flex; align-items: center; gap: 8px; }
    .dk-act form { margin: 0; }
    .dk-b { display: inline-flex; align-items: center; gap: 6px; height: 34px; padding: 0 12px; border: 0; border-radius: 10px; font: 600 12px 'Poppins', sans-serif; text-decoration: none; cursor: pointer; transition: transform .2s, background .2s; }
    .dk-b svg.i { width: 15px; height: 15px; }
    .dk-b:hover { transform: translateY(-2px); }
    .dk-b.e { color: var(--pri); background: #eef2ff; }
    .dk-b.e:hover { background: #e0e7ff; }
    .dk-b.d { color: #dc2626; background: #fef2f2; }
    .dk-b.d:hover { background: #fee2e2; }
    .dk-empty { padding: 44px 24px; text-align: center; color: var(--mut); font-size: 13px; line-height: 1.8; }
    .dk-empty svg { width: 84px; height: 84px; margin-bottom: 8px; }
    .dk-empty b { display: block; color: var(--ink); font-size: 15px; }

    @media (max-width: 760px) {
        .dk { padding: 20px 12px 40px; }
        .dk-hero { flex-direction: column; align-items: flex-start; }
        .dk-btn { width: 100%; }
        .dk-sum { gap: 10px; }
        .dk-sum div { padding-left: 10px; }
        .dk-sum b { font-size: 20px; }
        .dk-ch { padding: 16px; }
        .dk-s { width: 100%; }
        .dk thead { display: none; }
        .dk tbody tr { display: block; padding: 14px 16px; border-top: 1px solid #f0eefb; }
        .dk td { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 6px 0; border: 0; text-align: right; }
        .dk td::before { content: attr(data-l); color: #94a3b8; font-size: 11.5px; font-weight: 500; text-align: left; }
        .dk td.nm { display: block; padding-bottom: 10px; text-align: left; }
        .dk td.nm::before { content: none; }
        .dk td:last-child { padding-top: 10px; }
    }
    @media (prefers-reduced-motion: reduce) { .dk *, .dk *::before, .dk *::after { animation: none !important; transition: none !important; } }
</style>

<div class="dk">
    <div class="dk-bg" aria-hidden="true"><i class="dk-o1"></i><i class="dk-o2"></i><div class="dk-gridbg"></div></div>

    <div class="dk-wrap">

        <section class="dk-hero dk-fi">
            <div>
                <h1>Data dosen</h1>
                <p>Kelola dosen yang terdaftar di kampus Anda, lengkap dengan jumlah mahasiswa bimbingannya.</p>
            </div>
            <a href="{{ route('admin-kampus.dosen.create') }}" class="dk-btn"><svg class="i" viewBox="0 0 24 24">{!! $ic['plus'] !!}</svg>Tambah dosen</a>
        </section>

        <div class="dk-sum dk-fi" style="--i:1">
            <div><small>Total dosen</small><b>{{ $total }}</b></div>
            <div><small>Mahasiswa bimbingan</small><b>{{ $totalMhs }}</b></div>
            <div><small>Rata-rata per dosen</small><b>{{ $rata !== null ? $rata : '—' }}</b></div>
        </div>

        <section class="dk-card dk-fi" style="--i:2">
            <div class="dk-ch">
                <div>
                    <h2>Daftar dosen</h2>
                    <small>{{ $total }} dosen terdaftar</small>
                </div>
                @if ($daftar->isNotEmpty())
                    <label class="dk-s">
                        <svg class="i" viewBox="0 0 24 24">{!! $ic['search'] !!}</svg>
                        <input type="search" id="dkSearch" placeholder="Cari nama, NIP, atau email..." aria-label="Cari dosen" autocomplete="off">
                    </label>
                @endif
            </div>

            @if ($daftar->isEmpty())
                <div class="dk-empty">
                    <svg viewBox="0 0 120 120" fill="none" stroke="#c4b5fd" stroke-width="1.5" aria-hidden="true"><polygon points="60,10 105,35 105,85 60,110 15,85 15,35"/><polygon points="60,30 87,45 87,75 60,90 33,75 33,45" stroke="#a78bfa"/><circle cx="60" cy="60" r="4" fill="#7C3AED" stroke="none"/></svg>
                    <b>Belum ada dosen di kampus ini</b>
                    Klik "Tambah dosen" untuk mendaftarkan dosen pertama.
                </div>
            @else
                <div class="dk-scroll">
                    <table>
                        <thead>
                            <tr><th>NIP</th><th>Dosen</th><th>Mahasiswa bimbingan</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($daftar as $i => $item)
                                @php $n = $jumlah[$i]; $email = $item->user->email ?? null; @endphp
                                <tr style="--i:{{ min($i, 10) }}" data-q="{{ \Illuminate\Support\Str::lower($item->nip.' '.$item->nama.' '.$email) }}">
                                    <td data-l="NIP">{{ $item->nip }}</td>
                                    <td class="nm">
                                        <div class="dk-p">
                                            <span class="dk-av">{{ strtoupper(mb_substr($item->nama, 0, 1)) }}</span>
                                            <div>
                                                <div class="dk-n">{{ $item->nama }}</div>
                                                <div class="dk-e">{{ $email ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-l="Mahasiswa bimbingan">
                                        <span class="dk-chip {{ $n ? '' : 'z' }}"><svg class="i" viewBox="0 0 24 24">{!! $ic['users'] !!}</svg>{{ $n }} mahasiswa</span>
                                    </td>
                                    <td>
                                        <div class="dk-act">
                                            <a href="{{ route('admin-kampus.dosen.edit', $item->id) }}" class="dk-b e"><svg class="i" viewBox="0 0 24 24">{!! $ic['edit'] !!}</svg>Edit</a>
                                            <form method="POST" action="{{ route('admin-kampus.dosen.destroy', $item->id) }}" data-nama="{{ $item->nama }}" onsubmit="return confirm('Hapus dosen ' + this.dataset.nama + '?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dk-b d"><svg class="i" viewBox="0 0 24 24">{!! $ic['trash'] !!}</svg>Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="dk-empty" id="dkNone" hidden><b>Tidak ada hasil</b>Coba kata kunci lain.</div>
                </div>
            @endif
        </section>

    </div>
</div>

<script>
(function () {
    var q = document.getElementById('dkSearch');
    if (!q) return;
    var rows = [].slice.call(document.querySelectorAll('tr[data-q]')), none = document.getElementById('dkNone');
    q.addEventListener('input', function () {
        var v = q.value.trim().toLowerCase(), shown = 0;
        rows.forEach(function (r) {
            var ok = !v || r.dataset.q.indexOf(v) > -1;
            r.hidden = !ok;
            if (ok) shown++;
        });
        none.hidden = shown > 0;
    });
})();
</script>

@endsection