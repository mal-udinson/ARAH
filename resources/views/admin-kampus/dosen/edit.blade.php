@extends('layouts.app')

@section('title', 'Edit Dosen - ARAH')

@section('content')

@php
    $val = ['nama' => old('nama', $dosen->nama), 'nip' => old('nip', $dosen->nip), 'email' => old('email', $dosen->user->email ?? '')];
    $fields = [
        ['nama',  'Nama',  'text',  'Masukkan nama lengkap', '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>', 'name'],
        ['nip',   'NIP',   'text',  'Masukkan NIP',          '<rect x="3" y="5" width="18" height="14" rx="3"/><circle cx="9" cy="11" r="2"/><path d="M6.5 16a3 3 0 0 1 5 0M14 10h4M14 14h4"/>', 'off'],
        ['email', 'Email', 'email', 'Masukkan email dosen',  '<rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 8l9 6 9-6"/>', 'off'],
    ];
@endphp

<style>
    .fm { --ink:#1E2A6B; --pri:#4F46E5; --vio:#7C3AED; --tx:#334155; --mut:#64748b; --line:#e5e3ff;
        position: relative; overflow: hidden; min-height: calc(100vh - 64px); padding: 28px 22px 56px; color: #fff;
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; background: linear-gradient(165deg, #1b1760 0%, #3a2ea6 50%, #6b35e0 100%); }
    .fm *, .fm *::before, .fm *::after { box-sizing: border-box; }
    :where(.fm) h1, :where(.fm) h2, :where(.fm) p { margin: 0; }
    .fm-bg { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
    .fm-o1, .fm-o2 { position: absolute; border-radius: 50%; filter: blur(80px); animation: fm-dr 28s ease-in-out infinite alternate; }
    .fm-o1 { width: 460px; height: 460px; top: -150px; left: -120px; background: #8b5cf6; opacity: .5; }
    .fm-o2 { width: 400px; height: 400px; bottom: -140px; right: -120px; background: #c026d3; opacity: .26; animation-duration: 32s; }
    @keyframes fm-dr { to { transform: translate(100px, 70px) scale(1.1); } }
    .fm-gridbg { position: absolute; inset: 0; background-size: 56px 56px; -webkit-mask-image: linear-gradient(#000 20%, transparent 95%); mask-image: linear-gradient(#000 20%, transparent 95%);
        background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px); }
    .fm-wrap { position: relative; z-index: 1; width: min(820px, 100%); margin: 0 auto; }
    .fm svg.i { width: 18px; height: 18px; flex-shrink: 0; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
    .fm-fi { animation: fm-fi .5s ease calc(var(--i, 0) * 80ms) backwards; }
    @keyframes fm-fi { from { opacity: 0; transform: translateY(14px); } }
    .fm a:focus-visible, .fm button:focus-visible { outline: 3px solid #c4b5fd; outline-offset: 2px; }

    .fm-back { display: inline-flex; align-items: center; gap: 8px; padding: 8px 14px; border: 1px solid rgba(255,255,255,.3); border-radius: 99px; background: rgba(255,255,255,.12); color: #fff; font-size: 12px; font-weight: 600; text-decoration: none; transition: background .2s, transform .2s; }
    .fm-back:hover { background: rgba(255,255,255,.22); transform: translateX(-2px); }
    .fm-back svg.i { width: 15px; height: 15px; }
    .fm-hero { margin: 22px 0 26px; }
    .fm-hero h1 { font-size: clamp(28px, 4.2vw, 42px); line-height: 1.15; font-weight: 700; letter-spacing: -.03em; }
    .fm-hero p { max-width: 520px; margin-top: 8px; color: rgba(255,255,255,.82); font-size: 14px; line-height: 1.75; }

    .fm-grid { display: grid; gap: 18px; }
    .fm-card { padding: 28px; color: var(--tx); background: #fff; border-radius: 22px; box-shadow: 0 18px 46px rgba(10,6,60,.28); }
    .fm-fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
    .fm-f label { display: block; margin-bottom: 6px; color: var(--ink); font-size: 12.5px; font-weight: 600; }
    .fm-w { position: relative; }
    .fm-ico { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); display: flex; color: #94a3b8; pointer-events: none; transition: color .2s; }
    .fm-w:focus-within .fm-ico { color: var(--pri); }
    .fm-in { width: 100%; height: 48px; padding: 0 16px 0 44px; border: 1.5px solid var(--line); border-radius: 13px; outline: 0; background: #faf9ff; color: var(--tx); font: 13px 'Poppins', sans-serif; transition: border-color .2s, box-shadow .2s, background .2s; }
    .fm-in::placeholder { color: #a3a3c2; }
    .fm-in:focus { background: #fff; border-color: #a5b4fc; box-shadow: 0 0 0 4px rgba(79,70,229,.1); }
    .fm-in.bad { border-color: #f87171; background: #fff7f7; }
    .fm-in.pw { padding-right: 48px; }
    .fm-in:-webkit-autofill { -webkit-box-shadow: 0 0 0 100px #faf9ff inset; -webkit-text-fill-color: var(--tx); }
    .fm-eye { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); width: 36px; height: 36px; display: grid; place-items: center; border: 0; border-radius: 10px; background: transparent; color: #94a3b8; cursor: pointer; transition: color .2s, background .2s; }
    .fm-eye:hover { color: var(--pri); background: #eef2ff; }
    .fm-err { margin-top: 6px; color: #dc2626; font-size: 12px; }
    .fm-hint { margin-top: 6px; color: var(--mut); font-size: 11.5px; }

    .fm-act { display: flex; align-items: center; justify-content: flex-end; gap: 10px; margin-top: 26px; padding-top: 20px; border-top: 1px dashed var(--line); }
    .fm-btn, .fm-cancel { display: inline-flex; align-items: center; justify-content: center; min-height: 44px; padding: 0 22px; border-radius: 13px; font: 600 12.5px 'Poppins', sans-serif; text-decoration: none; cursor: pointer; transition: transform .2s, opacity .2s; }
    .fm-btn { border: 0; color: #fff; background: linear-gradient(135deg, var(--ink), var(--pri)); box-shadow: 0 10px 24px rgba(30,42,107,.3); }
    .fm-btn:hover:not(:disabled), .fm-cancel:hover { transform: translateY(-2px); }
    .fm-btn:disabled { opacity: .7; cursor: wait; }
    .fm-cancel { color: var(--pri); background: #eef2ff; }

    .fm-side { padding: 22px; border: 1px solid rgba(255,255,255,.26); border-radius: 22px; backdrop-filter: blur(18px);
        background: linear-gradient(135deg, rgba(255,255,255,.17), rgba(255,255,255,.06)); box-shadow: 0 24px 60px rgba(10,6,60,.3), inset 0 1px 0 rgba(255,255,255,.35); }
    .fm-side h2 { font-size: 15px; font-weight: 700; }
    .fm-side ul { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px 28px; margin: 12px 0 0; padding: 0; list-style: none; }
    .fm-side li { position: relative; padding-left: 18px; color: rgba(255,255,255,.85); font-size: 12.5px; line-height: 1.7; }
    .fm-side li::before { content: ""; position: absolute; left: 0; top: 9px; width: 7px; height: 7px; border-radius: 50%; background: #c4b5fd; }
    .fm-side p { margin-top: 14px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,.2); color: rgba(255,255,255,.75); font-size: 12px; line-height: 1.7; }
    .fm-me { display: flex; align-items: center; gap: 12px; }
    .fm-av { width: 44px; height: 44px; flex-shrink: 0; display: grid; place-items: center; border-radius: 14px; background: linear-gradient(135deg, var(--pri), var(--vio)); font-size: 17px; font-weight: 700; box-shadow: 0 8px 18px rgba(10,6,60,.3); }
    .fm-me b { display: block; font-size: 14px; overflow-wrap: anywhere; }
    .fm-me small, .fm-st small { color: rgba(255,255,255,.7); font-size: 12px; }
    .fm-st { margin-top: 16px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,.2); }
    .fm-st b { display: block; margin-top: 2px; font-size: 24px; font-weight: 700; }
    .fm-side.row { display: flex; align-items: center; flex-wrap: wrap; gap: 16px 28px; }
    .fm-side.row .fm-st { margin: 0; padding: 0 0 0 28px; border-top: 0; border-left: 1px solid rgba(255,255,255,.2); }
    .fm-side.row p { flex: 1 1 220px; margin: 0; padding: 0 0 0 28px; border-top: 0; border-left: 1px solid rgba(255,255,255,.2); }

    @media (max-width: 600px) {
        .fm { padding: 20px 12px 40px; }
        .fm-card { padding: 22px 16px; }
        .fm-side ul { grid-template-columns: 1fr; }
        .fm-side.row .fm-st, .fm-side.row p { flex-basis: 100%; padding: 14px 0 0; border-left: 0; border-top: 1px solid rgba(255,255,255,.2); }
        .fm-fields { grid-template-columns: 1fr; gap: 16px; }
        .fm-act { flex-direction: column-reverse; }
        .fm-btn, .fm-cancel { width: 100%; }
    }
    @media (prefers-reduced-motion: reduce) { .fm *, .fm *::before, .fm *::after { animation: none !important; transition: none !important; } }
</style>

<div class="fm">
    <div class="fm-bg" aria-hidden="true"><i class="fm-o1"></i><i class="fm-o2"></i><div class="fm-gridbg"></div></div>

    <div class="fm-wrap">

        <a href="{{ route('admin-kampus.dosen.index') }}" class="fm-back fm-fi"><svg class="i" viewBox="0 0 24 24"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>Kembali ke data dosen</a>

        <section class="fm-hero fm-fi" style="--i:1">
            <h1>Edit dosen</h1>
            <p>Perbarui data dosen.</p>
        </section>

        <div class="fm-grid">

            <aside class="fm-side row fm-fi" style="--i:2">
                <div class="fm-me">
                    <span class="fm-av">{{ strtoupper(mb_substr($dosen->nama, 0, 1)) }}</span>
                    <div><b>{{ $dosen->nama }}</b><small>NIP {{ $dosen->nip }}</small></div>
                </div>
                <div class="fm-st"><small>Mahasiswa bimbingan</small><b>{{ $dosen->mahasiswa()->count() }}</b></div>
                <p>Password hanya berubah kalau kolom password baru diisi.</p>
            </aside>

            <form method="POST" action="{{ route('admin-kampus.dosen.update', $dosen->id) }}" class="fm-card fm-fi" style="--i:3" id="fmForm">
                @csrf
                @method('PUT')

                <div class="fm-fields">
                    @foreach ($fields as $f)
                        <div class="fm-f">
                            <label for="{{ $f[0] }}">{{ $f[1] }}</label>
                            <div class="fm-w">
                                <span class="fm-ico"><svg class="i" viewBox="0 0 24 24">{!! $f[4] !!}</svg></span>
                                <input id="{{ $f[0] }}" class="fm-in @error($f[0]) bad @enderror" type="{{ $f[2] }}" name="{{ $f[0] }}" value="{{ $val[$f[0]] }}" placeholder="{{ $f[3] }}" autocomplete="{{ $f[5] }}" required>
                            </div>
                            @error($f[0])<div class="fm-err">{{ $message }}</div>@enderror
                        </div>
                    @endforeach

                    <div class="fm-f">
                        <label for="password">Password baru</label>
                        <div class="fm-w">
                            <span class="fm-ico"><svg class="i" viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="9" rx="3"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg></span>
                            <input id="password" class="fm-in pw @error('password') bad @enderror" type="password" name="password" placeholder="Kosongkan jika tidak diubah" autocomplete="new-password" >
                            <button type="button" class="fm-eye" id="fmEye" aria-label="Tampilkan password"><svg class="i" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                        </div>
                        @error('password')<div class="fm-err">{{ $message }}</div>@enderror
                        <div class="fm-hint">Kosongkan jika tidak ingin mengubah password.</div>
                    </div>
                </div>

                <div class="fm-act">
                    <a href="{{ route('admin-kampus.dosen.index') }}" class="fm-cancel">Batal</a>
                    <button type="submit" class="fm-btn" id="fmSubmit">Simpan perubahan</button>
                </div>
            </form>


        </div>
    </div>
</div>

<script>
(function () {
    var form = document.getElementById('fmForm'), btn = document.getElementById('fmSubmit'), label = btn.textContent;
    document.getElementById('fmEye').addEventListener('click', function () {
        var i = document.getElementById('password'), hidden = i.type === 'password';
        i.type = hidden ? 'text' : 'password';
        this.setAttribute('aria-label', hidden ? 'Sembunyikan password' : 'Tampilkan password');
    });
    form.addEventListener('submit', function () { btn.disabled = true; btn.textContent = 'Menyimpan...'; });
    window.addEventListener('pageshow', function (e) { if (e.persisted) { btn.disabled = false; btn.textContent = label; } });
})();
</script>

@endsection