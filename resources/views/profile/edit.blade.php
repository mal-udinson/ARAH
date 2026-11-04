@extends('layouts.app')

@section('title', 'Data Diri — ARAH')

@section('content')

@php
    $inisial = strtoupper(mb_substr($user->name ?? 'A', 0, 1));
    $ipk     = (float) ($mahasiswa->ipk ?? 0);

    // Ringkasan di panel kiri dibaca dari data yang sudah tersimpan
    $kampusNama = ! empty($mahasiswa?->kampus_id) ? optional(\App\Models\Kampus::find($mahasiswa->kampus_id))->nama : null;
    $prodiNama  = (! empty($mahasiswa?->prodi_id) && class_exists(\App\Models\Prodi::class)) ? optional(\App\Models\Prodi::find($mahasiswa->prodi_id))->nama : null;

    // Pilihan untuk form: kampus aktif (ditambah kampus mahasiswa saat ini), dan semua prodi
    $kampusList = \App\Models\Kampus::where('status', true)
        ->when(! empty($mahasiswa?->kampus_id), fn ($q) => $q->orWhere('id', $mahasiswa->kampus_id))
        ->orderBy('nama')
        ->get(['id', 'nama']);

    $prodiList = class_exists(\App\Models\Prodi::class)
        ? \App\Models\Prodi::orderBy('nama')->get(['id', 'nama', 'kampus_id'])
        : collect();

    $rows = array_filter([
        ['Semester',      $mahasiswa->semester ?? '-'],
        ['IPK',           $ipk > 0 ? number_format($ipk, 2) : 'Belum diisi'],
        ['Kampus',        $kampusNama],
        ['Program studi', $prodiNama],
        ['Jenis kelamin', $mahasiswa?->jenis_kelamin],
    ], fn ($r) => $r[1] !== null && $r[1] !== '');

    $fields = [
        ['_h', 'Akun'],
        ['name',     'Nama lengkap', 'text',   $user->name,                 '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>', 'name',  '', true,  null],
        ['email',    'Email',        'email',  $user->email,                '<rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 8l9 6 9-6"/>', 'email', '', true,  null],
        ['_h', 'Data mahasiswa'],
        ['nim',      'NIM',          'text',   $mahasiswa->nim ?? '',       '<rect x="3" y="5" width="18" height="14" rx="3"/><circle cx="9" cy="11" r="2"/><path d="M6.5 16a3 3 0 0 1 5 0M14 10h4M14 14h4"/>', 'off', 'maxlength="30"', false, null],
        ['semester', 'Semester',     'select', $mahasiswa->semester ?? 1,   '<rect x="3" y="5" width="18" height="16" rx="3"/><path d="M3 10h18M8 3v4M16 3v4"/>', 'off', '', false, null],
        ['kampus_id', 'Kampus / Universitas', 'kampus', $mahasiswa->kampus_id ?? '', '<path d="M4 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16M16 9h2a2 2 0 0 1 2 2v10M2 21h20M8 7h4M8 11h4M8 15h4"/>', 'off', '', true, null],
        ['prodi_id',  'Program studi',        'prodi',  $mahasiswa->prodi_id ?? '',  '<path d="M4 5a2 2 0 0 1 2-2h13v16H6a2 2 0 0 0-2 2V5z"/><path d="M4 19a2 2 0 0 1 2-2h13"/>', 'off', '', true, 'Pilihan program studi menyesuaikan kampus yang dipilih.'],
        ['ipk',      'IPK',          'number', $mahasiswa->ipk ?? '',       '<path d="M12 3l9 5-9 5-9-5 9-5z"/><path d="M7 11v5c0 1.5 2.2 3 5 3s5-1.5 5-3v-5"/>', 'off', 'min="0" max="4" step="0.01"', true, 'Sesuaikan IPK dengan data di halaman Data akademik.'],
    ];
@endphp

<style>
    .pf { --ink:#1E2A6B; --pri:#4F46E5; --vio:#7C3AED; --tx:#334155; --mut:#64748b; --line:#e5e3ff;
        position: relative; overflow: hidden; min-height: calc(100vh - 64px); padding: 32px 22px 56px; color: #fff;
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; background: linear-gradient(165deg, #1b1760 0%, #3a2ea6 50%, #6b35e0 100%); }
    .pf *, .pf *::before, .pf *::after { box-sizing: border-box; }
    :where(.pf) h1, :where(.pf) h2, :where(.pf) p { margin: 0; }
    .pf-bg { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
    .pf-o1, .pf-o2 { position: absolute; border-radius: 50%; filter: blur(80px); animation: pf-dr 28s ease-in-out infinite alternate; }
    .pf-o1 { width: 460px; height: 460px; top: -150px; left: -120px; background: #8b5cf6; opacity: .5; }
    .pf-o2 { width: 400px; height: 400px; bottom: -140px; right: -120px; background: #c026d3; opacity: .26; animation-duration: 32s; }
    @keyframes pf-dr { to { transform: translate(100px, 70px) scale(1.1); } }
    .pf-gridbg { position: absolute; inset: 0; background-size: 56px 56px; -webkit-mask-image: linear-gradient(#000 20%, transparent 95%); mask-image: linear-gradient(#000 20%, transparent 95%);
        background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px); }
    .pf-wrap { position: relative; z-index: 1; width: min(1020px, 100%); margin: 0 auto; }
    .pf svg.i { width: 18px; height: 18px; flex-shrink: 0; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
    .pf-fi { animation: pf-fi .5s ease calc(var(--i, 0) * 80ms) backwards; }
    @keyframes pf-fi { from { opacity: 0; transform: translateY(14px); } }
    .pf button:focus-visible, .pf input:focus-visible, .pf select:focus-visible { outline: 3px solid #c4b5fd; outline-offset: 2px; }

    .pf-hero { margin-bottom: 26px; }
    .pf-hero h1 { font-size: clamp(28px, 4.2vw, 42px); line-height: 1.15; font-weight: 700; letter-spacing: -.03em; }
    .pf-hero p { max-width: 560px; margin-top: 8px; color: rgba(255,255,255,.82); font-size: 14px; line-height: 1.75; }

    .pf-grid { display: grid; grid-template-columns: 300px minmax(0, 1fr); gap: 18px; align-items: start; }

    /* Ringkasan (panel kaca) */
    .pf-side { position: sticky; top: 88px; padding: 28px 24px; text-align: center; border: 1px solid rgba(255,255,255,.26); border-radius: 26px; backdrop-filter: blur(18px);
        background: linear-gradient(135deg, rgba(255,255,255,.17), rgba(255,255,255,.06)); box-shadow: 0 30px 70px rgba(10,6,60,.35), inset 0 1px 0 rgba(255,255,255,.35); }
    .pf-av { width: 92px; height: 92px; margin: 0 auto 14px; display: grid; place-items: center; border-radius: 28px; background: linear-gradient(135deg, var(--pri), var(--vio)); font-size: 38px; font-weight: 700; box-shadow: 0 16px 32px rgba(10,6,60,.4); }
    .pf-nm { font-size: 18px; font-weight: 700; overflow-wrap: anywhere; }
    .pf-nim { margin-top: 3px; color: rgba(255,255,255,.7); font-size: 12px; }
    .pf-role { display: inline-block; margin-top: 12px; padding: 4px 13px; border-radius: 99px; background: rgba(255,255,255,.16); font-size: 11.5px; font-weight: 600; }
    .pf-info { margin-top: 20px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,.2); text-align: left; }
    .pf-row { display: flex; justify-content: space-between; gap: 14px; padding: 8px 0; font-size: 12.5px; }
    .pf-row span:first-child { color: rgba(255,255,255,.7); }
    .pf-row span:last-child { font-weight: 600; text-align: right; overflow-wrap: anywhere; }

    /* Form */
    .pf-card { padding: 28px; color: var(--tx); background: #fff; border-radius: 22px; box-shadow: 0 18px 46px rgba(10,6,60,.28); }
    .pf-card h2 { color: var(--ink); font-size: 18px; font-weight: 700; letter-spacing: -.01em; }
    .pf-sub { margin: 4px 0 18px; color: var(--mut); font-size: 12.5px; line-height: 1.6; }
    .pf-alert { margin-bottom: 18px; padding: 12px 15px; border-left: 4px solid; border-radius: 12px; font-size: 12.5px; line-height: 1.6; }
    .pf-alert.ok { color: #15803d; background: #f0fdf4; border-color: #10b981; }
    .pf-alert.er { color: #b91c1c; background: #fef2f2; border-color: #ef4444; }
    .pf-fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 18px; }
    .pf-sec { grid-column: 1 / -1; margin-top: 6px; padding-bottom: 8px; border-bottom: 1px dashed var(--line); color: var(--ink); font-size: 13px; font-weight: 700; }
    .pf-sec:first-child { margin-top: 0; }
    .pf-f.full { grid-column: 1 / -1; }
    .pf-f label { display: block; margin-bottom: 6px; color: var(--ink); font-size: 12.5px; font-weight: 600; }
    .pf-w { position: relative; }
    .pf-ico { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); display: flex; color: #94a3b8; pointer-events: none; transition: color .2s; }
    .pf-w:focus-within .pf-ico { color: var(--pri); }
    .pf-in { width: 100%; height: 48px; padding: 0 16px 0 44px; border: 1.5px solid var(--line); border-radius: 13px; outline: 0; background: #faf9ff; color: var(--tx); font: 13px 'Poppins', sans-serif; transition: border-color .2s, box-shadow .2s, background .2s; }
    .pf-in:focus { background: #fff; border-color: #a5b4fc; box-shadow: 0 0 0 4px rgba(79,70,229,.1); }
    .pf-in.bad { border-color: #f87171; background: #fff7f7; }
    .pf-in:-webkit-autofill { -webkit-box-shadow: 0 0 0 100px #faf9ff inset; -webkit-text-fill-color: var(--tx); }
    select.pf-in { appearance: none; -webkit-appearance: none; padding-right: 44px; cursor: pointer; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 16px center; background-size: 16px; }
    .pf-err { margin-top: 6px; color: #dc2626; font-size: 12px; }
    .pf-hint { margin-top: 6px; color: var(--mut); font-size: 11.5px; }
    .pf-act { display: flex; justify-content: flex-end; margin-top: 24px; padding-top: 20px; border-top: 1px dashed var(--line); }
    .pf-btn { min-height: 46px; padding: 0 26px; border: 0; border-radius: 13px; color: #fff; background: linear-gradient(135deg, var(--ink), var(--pri)); box-shadow: 0 10px 24px rgba(30,42,107,.3); font: 600 13px 'Poppins', sans-serif; cursor: pointer; transition: transform .2s, opacity .2s; }
    .pf-btn:hover:not(:disabled) { transform: translateY(-2px); }
    .pf-btn:disabled { opacity: .7; cursor: wait; }

    @media (max-width: 860px) { .pf-grid { grid-template-columns: 1fr; } .pf-side { position: static; } }
    @media (max-width: 600px) {
        .pf { padding: 20px 12px 40px; }
        .pf-card { padding: 22px 16px; }
        .pf-fields { grid-template-columns: 1fr; }
        .pf-btn { width: 100%; }
    }
    @media (prefers-reduced-motion: reduce) { .pf *, .pf *::before, .pf *::after { animation: none !important; transition: none !important; } }
</style>

<div class="pf">
    <div class="pf-bg" aria-hidden="true"><i class="pf-o1"></i><i class="pf-o2"></i><div class="pf-gridbg"></div></div>

    <div class="pf-wrap">

        <section class="pf-hero pf-fi">
            <h1>Data diri</h1>
            <p>Kelola informasi akun dan data mahasiswa yang dipakai ARAH untuk memberi pengalaman akademik yang lebih personal.</p>
        </section>

        <div class="pf-grid">

            <aside class="pf-side pf-fi" style="--i:1">
                <div class="pf-av">{{ $inisial }}</div>
                <div class="pf-nm">{{ $user->name }}</div>
                <div class="pf-nim">{{ ! empty($mahasiswa?->nim) ? $mahasiswa->nim : 'NIM belum diisi' }}</div>
                <span class="pf-role">Mahasiswa</span>
                <div class="pf-info">
                    @foreach ($rows as $r)
                        <div class="pf-row"><span>{{ $r[0] }}</span><span>{{ $r[1] }}</span></div>
                    @endforeach
                </div>
            </aside>

            <section class="pf-card pf-fi" style="--i:2">
                <h2>Informasi pribadi</h2>
                <p class="pf-sub">Perubahan yang disimpan langsung dipakai oleh sistem ARAH.</p>

                @if (session('status') === 'profile-updated')
                    <div class="pf-alert ok" role="status">Data diri berhasil diperbarui.</div>
                @endif
                @if ($errors->any())
                    <div class="pf-alert er" role="alert">Ada data yang belum sesuai. Silakan periksa kembali.</div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" id="pfForm">
                    @csrf
                    @method('PATCH')

                    <div class="pf-fields">
                        @foreach ($fields as $f)
                            @if ($f[0] === '_h')
                                <div class="pf-sec">{{ $f[1] }}</div>
                                @continue
                            @endif
                            <div class="pf-f {{ $f[7] ? 'full' : '' }}">
                                <label for="{{ $f[0] }}">{{ $f[1] }}</label>
                                <div class="pf-w">
                                    <span class="pf-ico"><svg class="i" viewBox="0 0 24 24">{!! $f[4] !!}</svg></span>
                                    @if ($f[2] === 'select')
                                        <select id="{{ $f[0] }}" name="{{ $f[0] }}" class="pf-in @error($f[0]) bad @enderror" required>
                                            @for ($s = 1; $s <= 14; $s++)
                                                <option value="{{ $s }}" @selected((int) old($f[0], $f[3]) === $s)>Semester {{ $s }}</option>
                                            @endfor
                                        </select>
                                    @elseif ($f[2] === 'kampus')
                                        <select id="{{ $f[0] }}" name="{{ $f[0] }}" class="pf-in @error($f[0]) bad @enderror" required>
                                            <option value="" disabled @selected(! old($f[0], $f[3]))>Pilih kampus</option>
                                            @foreach ($kampusList as $k)
                                                <option value="{{ $k->id }}" @selected((string) old($f[0], $f[3]) === (string) $k->id)>{{ $k->nama }}</option>
                                            @endforeach
                                        </select>
                                    @elseif ($f[2] === 'prodi')
                                        <select id="{{ $f[0] }}" name="{{ $f[0] }}" class="pf-in @error($f[0]) bad @enderror" required>
                                            <option value="" disabled @selected(! old($f[0], $f[3]))>Pilih program studi</option>
                                            @foreach ($prodiList as $p)
                                                <option value="{{ $p->id }}" data-kampus="{{ $p->kampus_id }}" @selected((string) old($f[0], $f[3]) === (string) $p->id)>{{ $p->nama }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input id="{{ $f[0] }}" name="{{ $f[0] }}" type="{{ $f[2] }}" class="pf-in @error($f[0]) bad @enderror" value="{{ old($f[0], $f[3]) }}" autocomplete="{{ $f[5] }}" {!! $f[6] !!} required>
                                    @endif
                                </div>
                                @error($f[0])<div class="pf-err">{{ $message }}</div>@enderror
                                @if (! empty($f[8]))<div class="pf-hint">{{ $f[8] }}</div>@endif
                            </div>
                        @endforeach
                    </div>

                    <div class="pf-act">
                        <button type="submit" class="pf-btn" id="pfSubmit">Simpan perubahan</button>
                    </div>
                </form>
            </section>

        </div>
    </div>
</div>

<script>
(function () {
    var form = document.getElementById('pfForm'), btn = document.getElementById('pfSubmit'), label = btn.textContent;
    form.addEventListener('submit', function () { btn.disabled = true; btn.textContent = 'Menyimpan...'; });
    window.addEventListener('pageshow', function (e) { if (e.persisted) { btn.disabled = false; btn.textContent = label; } });

    // Program studi mengikuti kampus: tampilkan prodi milik kampus terpilih + prodi umum (tanpa kampus)
    var kampus = document.getElementById('kampus_id'), prodi = document.getElementById('prodi_id');
    if (kampus && prodi) {
        var semua = Array.prototype.slice.call(prodi.options, 1).map(function (o) {
            return { id: o.value, nama: o.textContent, kampus: o.getAttribute('data-kampus') || '' };
        });
        var isi = function () {
            var k = kampus.value, pilih = prodi.value;
            while (prodi.options.length > 1) prodi.remove(1);
            semua.forEach(function (p) {
                if (p.kampus && p.kampus !== k) return;
                var o = new Option(p.nama, p.id);
                o.setAttribute('data-kampus', p.kampus);
                prodi.add(o);
            });
            prodi.value = pilih;
            if (prodi.value !== pilih) prodi.selectedIndex = 0;
        };
        kampus.addEventListener('change', isi);
        isi();
    }
})();
</script>

@endsection