@extends('layouts.app')

@section('title', 'Detail Mahasiswa - ARAH')

@section('content')

@php
    $ipk   = (float) $mahasiswa->ipk;
    $stIpk = $ipk <= 0 ? ['Belum ada data', 'n'] : ($ipk >= 3.5 ? ['Sangat baik', 'g'] : ($ipk >= 3 ? ['Baik', 'b'] : ($ipk >= 2.75 ? ['Cukup', 'a'] : ['IPK rendah', 'r'])));
    $daftarNilai = $mahasiswa->nilai;
    $totalSks    = $daftarNilai->sum(fn ($n) => $n->mataKuliah->sks ?? 0);
    $rataNilai   = $daftarNilai->count() ? $daftarNilai->avg('nilai_angka') : null;
    $skip = ['id', 'mahasiswa_id', 'mata_kuliah_id', 'created_at', 'updated_at'];
    $tone = fn ($h) => in_array($h, ['A', 'AB']) ? 'g' : (in_array($h, ['B', 'BC']) ? 'b' : ($h === 'C' ? 'a' : 'r'));
    $ic = [
        'cal'   => '<rect x="3" y="5" width="18" height="16" rx="3"/><path d="M3 10h18M8 3v4M16 3v4"/>',
        'bars'  => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
        'build' => '<path d="M4 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16M16 9h2a2 2 0 0 1 2 2v10M2 21h20M8 7h4M8 11h4M8 15h4"/>',
        'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.5a3.5 3.5 0 0 1 0 7M18 14a6.5 6.5 0 0 1 3.5 6"/>',
    ];
    $stats = [
        ['Semester', 'cal', 'Semester ' . $mahasiswa->semester, null],
        ['IPK', 'bars', $ipk > 0 ? number_format($ipk, 2) : '—', $stIpk],
        ['Kampus', 'build', $mahasiswa->kampus->kode ?? '-', null],
        ['Dosen PA', 'users', $dosen->nama, null],
    ];
@endphp

<style>
    .dm { --ink:#1E2A6B; --pri:#4F46E5; --sky:#0ea5e9; --tx:#334155; --mut:#64748b; --line:#e0e7ff; --soft:#f5f7ff;
        position: relative; overflow: hidden; min-height: calc(100vh - 64px); padding: 28px 22px 60px; color: var(--tx);
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; background: linear-gradient(165deg, #14235f 0%, #3a3db5 52%, #0b8ad6 100%); }
    .dm *, .dm *::before, .dm *::after { box-sizing: border-box; }
    .dm-wrap { position: relative; z-index: 2; width: min(1280px, 100%); margin: 0 auto; }
    .dm .i { width: 20px; height: 20px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }

    .dm-bg { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
    .dm-orb { position: absolute; border-radius: 50%; filter: blur(80px); animation: dm-dr 24s ease-in-out infinite alternate; }
    .dm-o1 { width: 480px; height: 480px; top: -160px; left: -140px; background: #6366f1; opacity: .5; }
    .dm-o2 { width: 420px; height: 420px; bottom: -150px; right: -120px; background: #38bdf8; opacity: .4; animation-direction: alternate-reverse; }
    @keyframes dm-dr { to { transform: translate(120px, 90px) scale(1.15); } }
    .dm-grid { position: absolute; inset: 0; background-size: 56px 56px; animation: dm-gm 40s linear infinite;
        background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
        -webkit-mask-image: linear-gradient(#000 20%, transparent 95%); mask-image: linear-gradient(#000 20%, transparent 95%); }
    @keyframes dm-gm { to { background-position: 0 56px, 56px 0; } }

    .dm-back { display: inline-flex; align-items: center; gap: 8px; padding: 8px 14px; border-radius: 99px; background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.26); color: #fff; font-size: 12px; font-weight: 600; text-decoration: none; transition: background .2s, transform .2s; }
    .dm-back:hover { background: rgba(255,255,255,.24); transform: translateX(-3px); }

    .dm-hero { display: flex; align-items: center; justify-content: space-between; gap: 24px; margin: 20px 0 26px; color: #fff; }
    .dm-id { display: flex; align-items: center; gap: 20px; min-width: 0; }
    .dm-av { width: 84px; height: 84px; flex-shrink: 0; display: grid; place-items: center; border-radius: 26px; font-size: 34px; font-weight: 700; color: #fff; background: linear-gradient(135deg, #6366f1, #a855f7); border: 2px solid rgba(255,255,255,.4); box-shadow: 0 16px 34px rgba(8,16,70,.4); }
    .dm-hero h1 { margin: 0 0 10px; font-size: clamp(26px, 4vw, 42px); line-height: 1.12; font-weight: 700; letter-spacing: -.03em; }
    .dm-chips { display: flex; flex-wrap: wrap; gap: 8px; }
    .dm-chip { padding: 5px 12px; border-radius: 99px; background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.26); font-size: 11.5px; font-weight: 500; }
    .dm-mascot { width: 150px; height: 150px; object-fit: contain; flex-shrink: 0; filter: drop-shadow(0 16px 26px rgba(8,16,70,.45)); animation: dm-bob 4s ease-in-out infinite; }
    @keyframes dm-bob { 0%, 100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-9px) rotate(2deg); } }

    .dm-card { background: #fff; border: 1px solid rgba(255,255,255,.7); border-radius: 22px; box-shadow: 0 18px 46px rgba(8,16,70,.28); animation: dm-in .6s ease both; }
    @keyframes dm-in { from { opacity: 0; transform: translateY(16px); } }
    .dm-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-bottom: 20px; }
    .dm-stat { padding: 20px; transition: transform .25s, box-shadow .25s; }
    .dm-stat:hover { transform: translateY(-4px); box-shadow: 0 26px 56px rgba(8,16,70,.36); }
    .dm-ico { width: 44px; height: 44px; display: grid; place-items: center; border-radius: 14px; color: var(--pri); background: linear-gradient(135deg, #eef2ff, #e0f2fe); }
    .dm-lb { margin-top: 14px; color: var(--mut); font-size: 12px; font-weight: 500; }
    .dm-val { margin-top: 2px; color: var(--ink); font-size: 22px; line-height: 1.25; font-weight: 700; letter-spacing: -.02em; overflow-wrap: anywhere; }
    .dm-stat .dm-bd { margin-top: 8px; display: inline-block; }

    .dm-cols { display: grid; grid-template-columns: minmax(0, 1.55fr) minmax(0, 1fr); gap: 18px; margin-bottom: 18px; align-items: stretch; grid-auto-rows: minmax(540px, auto); }
    .dm-cols > .dm-pn { display: flex; flex-direction: column; min-height: 0; }
    .dm-pn { padding: 22px 24px; }
    .dm-h { margin: 0; color: var(--ink); font-size: 17px; font-weight: 700; }
    .dm-h em { margin-left: 8px; padding: 3px 10px; border-radius: 99px; background: #eef2ff; color: var(--pri); font-style: normal; font-size: 11px; font-weight: 600; vertical-align: 2px; }
    .dm-sub { margin: 4px 0 16px; color: var(--mut); font-size: 12.5px; }
    .dm-empty { padding: 34px 14px; text-align: center; color: #94a3b8; font-size: 12.5px; line-height: 1.8; border: 1.5px dashed #ddd9ff; border-radius: 16px; background: #faf9ff; }

    .dm-bd { padding: 3px 10px; border-radius: 99px; font-size: 11px; font-weight: 700; white-space: nowrap; }
    .dm-bd.g { background: #d1fae5; color: #047857; }
    .dm-bd.b { background: #e0e7ff; color: #4338ca; }
    .dm-bd.a { background: #fef3c7; color: #b45309; }
    .dm-bd.r { background: #ffe4e6; color: #be123c; }
    .dm-bd.n { background: #f1f5f9; color: #64748b; }

    .dm-sum { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; }
    .dm-sum span { padding: 5px 12px; border-radius: 99px; background: var(--soft); color: var(--mut); font-size: 11.5px; }
    .dm-sum b { color: var(--ink); }
    .dm-fit { position: relative; flex: 1; min-height: 240px; }
    .dm-scroll { position: absolute; inset: 0; overflow: auto; border: 1px solid #eef1fb; border-radius: 16px; scrollbar-width: thin; scrollbar-color: #c7c2ff transparent; }
    .dm table { width: 100%; min-width: 560px; border-collapse: collapse; }
    .dm th { position: sticky; top: 0; z-index: 1; padding: 12px 16px; background: var(--soft); color: var(--mut); font-size: 11px; font-weight: 600; text-align: left; }
    .dm td { padding: 13px 16px; border-top: 1px solid #eef1fb; font-size: 13px; vertical-align: middle; }
    .dm tbody tr:hover { background: #faf9ff; }
    .dm .c { text-align: center; }
    .dm-code { padding: 3px 9px; border-radius: 8px; background: #eef2ff; color: var(--pri); font-size: 11.5px; font-weight: 600; }
    .dm-mk { color: var(--ink); font-weight: 600; }
    .dm-ang { margin-top: 3px; color: #94a3b8; font-size: 11px; }

    .dm-bar { display: grid; grid-template-columns: 1fr 42px; align-items: center; gap: 4px 10px; }
    .dm-bar + .dm-bar { margin-top: 14px; }
    .dm-bar > span { grid-column: 1 / -1; color: var(--tx); font-size: 12.5px; font-weight: 600; }
    .dm-tr { height: 8px; overflow: hidden; border-radius: 99px; background: #e8edff; }
    .dm-fill { height: 100%; width: var(--w); border-radius: inherit; background: linear-gradient(90deg, var(--pri), var(--sky)); animation: dm-grow 1.2s cubic-bezier(.2,.8,.2,1) .2s both; }
    @keyframes dm-grow { from { width: 0; } }
    .dm-bar b { text-align: right; color: var(--pri); font-size: 12px; }
    .dm-kv { display: flex; justify-content: space-between; gap: 16px; padding: 8px 0; border-bottom: 1px solid #eef1fb; font-size: 12px; }
    .dm-kv span:first-child { color: var(--mut); }
    .dm-kv span:last-child { color: var(--ink); font-weight: 600; text-align: right; }
    .dm-blk { padding: 6px 14px; margin-bottom: 10px; border: 1px solid #eef1fb; border-radius: 14px; background: #faf9ff; }

    .dm-recs { display: grid; grid-template-columns: repeat(auto-fit, minmax(270px, 1fr)); gap: 14px; }
    .dm-rec { display: flex; flex-direction: column; gap: 10px; padding: 18px; border: 1px solid var(--line); border-radius: 18px; background: linear-gradient(180deg, #fff, #faf9ff); transition: transform .25s, box-shadow .25s; }
    .dm-rec:hover { transform: translateY(-3px); box-shadow: 0 16px 34px rgba(79,70,229,.16); }
    .dm-rec h3 { margin: 0; color: var(--ink); font-size: 14.5px; font-weight: 700; }
    .dm-top { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
    .dm-alasan { margin: 0; color: var(--tx); font-size: 12.5px; line-height: 1.7; }
    .dm-meta { display: flex; flex-wrap: wrap; gap: 6px; }
    .dm-meta span { padding: 3px 10px; border-radius: 99px; background: var(--soft); color: var(--mut); font-size: 11px; }
    .dm-meta b { color: var(--tx); font-weight: 600; }

    @media (max-width: 1000px) { .dm-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } .dm-cols { grid-template-columns: 1fr; grid-auto-rows: auto; } .dm-fit { flex: none; height: 440px; } }
    @media (max-width: 640px) { .dm { padding: 18px 12px 40px; } .dm-mascot { display: none; } .dm-av { width: 64px; height: 64px; font-size: 26px; border-radius: 20px; } .dm-pn { padding: 18px; } }
    @media (max-width: 460px) { .dm-stats { gap: 9px; } .dm-stat { padding: 14px; } }
    @media (prefers-reduced-motion: reduce) { .dm *, .dm *::before, .dm *::after { animation: none !important; transition: none !important; } }
</style>

<div class="dm">

    <div class="dm-bg" aria-hidden="true"><div class="dm-grid"></div><i class="dm-orb dm-o1"></i><i class="dm-orb dm-o2"></i></div>

    <div class="dm-wrap">

        <a href="{{ route('dosen.dashboard') }}" class="dm-back">← Kembali ke Dashboard</a>

        {{-- HERO --}}
        <section class="dm-hero">
            <div class="dm-id">
                <div class="dm-av">{{ strtoupper(mb_substr($mahasiswa->nama, 0, 1)) }}</div>
                <div>
                    <h1>{{ $mahasiswa->nama }}</h1>
                    <div class="dm-chips">
                        <span class="dm-chip">NIM {{ $mahasiswa->nim }}</span>
                        @if ($mahasiswa->user)<span class="dm-chip">{{ $mahasiswa->user->email }}</span>@endif
                    </div>
                </div>
            </div>
            <img class="dm-mascot" src="{{ asset('images/happy.png') }}" alt="NARA" onerror="this.style.display='none'">
        </section>

        {{-- RINGKASAN --}}
        <div class="dm-stats">
            @foreach ($stats as $s)
                <div class="dm-card dm-stat">
                    <span class="dm-ico"><svg class="i" viewBox="0 0 24 24">{!! $ic[$s[1]] !!}</svg></span>
                    <div class="dm-lb">{{ $s[0] }}</div>
                    <div class="dm-val">{{ $s[2] }}</div>
                    @if ($s[3])<span class="dm-bd {{ $s[3][1] }}">{{ $s[3][0] }}</span>@endif
                </div>
            @endforeach
        </div>

        <div class="dm-cols">

            {{-- DATA AKADEMIK --}}
            <section class="dm-card dm-pn">
                <h2 class="dm-h">Data akademik<em>{{ $daftarNilai->count() }}</em></h2>
                <p class="dm-sub">Riwayat nilai mahasiswa yang tersimpan di sistem.</p>

                @if ($daftarNilai->count() > 0)
                    <div class="dm-sum">
                        <span>Total SKS <b>{{ $totalSks }}</b></span>
                        <span>Rata-rata nilai <b>{{ number_format($rataNilai, 2) }}</b></span>
                    </div>
                    <div class="dm-fit"><div class="dm-scroll">
                        <table>
                            <thead><tr><th>Kode</th><th>Mata kuliah</th><th class="c">SKS</th><th class="c">Nilai</th></tr></thead>
                            <tbody>
                                @foreach ($daftarNilai as $n)
                                    <tr>
                                        <td><span class="dm-code">{{ $n->mataKuliah->kode ?? '-' }}</span></td>
                                        <td class="dm-mk">{{ $n->mataKuliah->nama ?? '-' }}</td>
                                        <td class="c">{{ $n->mataKuliah->sks ?? '-' }}</td>
                                        <td class="c">
                                            <span class="dm-bd {{ $tone($n->nilai_huruf) }}">{{ $n->nilai_huruf ?? '-' }}</span>
                                            <div class="dm-ang">{{ $n->nilai_angka ?? '-' }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div></div>
                @else
                    <div class="dm-empty">Belum ada data akademik.</div>
                @endif
            </section>

            {{-- PROFIL MINAT --}}
            <section class="dm-card dm-pn">
                <h2 class="dm-h">Profil minat &amp; asesmen<em>{{ $mahasiswa->asesmen->count() }}</em></h2>
                <p class="dm-sub">Hasil asesmen yang digunakan ARAH dalam analisis akademik.</p>

                @forelse ($mahasiswa->asesmen as $as)
                    @php $d = collect($as->toArray())->except($skip)->filter(fn ($v) => ! is_array($v) && ! is_object($v)); @endphp
                    @if ($d->has('kategori') && is_numeric($d->get('skor')))
                        <div class="dm-bar">
                            <span>{{ $d->get('kategori') }}</span>
                            <div class="dm-tr"><div class="dm-fill" style="--w:{{ min(100, max(0, round($d->get('skor')))) }}%"></div></div>
                            <b>{{ round($d->get('skor')) }}%</b>
                        </div>
                    @else
                        <div class="dm-blk">
                            @foreach ($d as $key => $value)
                                <div class="dm-kv"><span>{{ ucwords(str_replace('_', ' ', $key)) }}</span><span>{{ $value }}</span></div>
                            @endforeach
                        </div>
                    @endif
                @empty
                    <div class="dm-empty">Mahasiswa belum mengisi asesmen.</div>
                @endforelse
            </section>
        </div>

        {{-- REKOMENDASI --}}
        <section class="dm-card dm-pn">
            <h2 class="dm-h">Rekomendasi ARAH<em>{{ $mahasiswa->rekomendasi->count() }}</em></h2>
            <p class="dm-sub">Hasil rekomendasi AI yang tersedia untuk mahasiswa.</p>

            @if ($mahasiswa->rekomendasi->count() > 0)
                <div class="dm-recs">
                    @foreach ($mahasiswa->rekomendasi as $rek)
                        @php
                            $d = collect($rek->toArray())->except($skip)->filter(fn ($v) => ! is_array($v) && ! is_object($v));
                            $skorKey = $d->keys()->first(fn ($k) => preg_match('/skor|kecocokan|persen/', $k) && is_numeric($d[$k]));
                            $pct = $skorKey ? ((float) $d[$skorKey] <= 1 ? (float) $d[$skorKey] * 100 : (float) $d[$skorKey]) : null;
                            $alasanKey = $d->keys()->first(fn ($k) => str_contains($k, 'alasan'));
                            $sisa = $d->except(array_filter([$skorKey, $alasanKey]));
                        @endphp
                        <div class="dm-rec">
                            <div class="dm-top">
                                <h3>{{ $rek->mataKuliah->nama ?? 'Mata kuliah' }}</h3>
                                <span class="dm-code">{{ $rek->mataKuliah->kode ?? '-' }}</span>
                            </div>
                            @if ($pct !== null)
                                <div class="dm-bar">
                                    <span>{{ ucwords(str_replace('_', ' ', $skorKey)) }}</span>
                                    <div class="dm-tr"><div class="dm-fill" style="--w:{{ min(100, max(0, round($pct))) }}%"></div></div>
                                    <b>{{ min(100, max(0, round($pct))) }}%</b>
                                </div>
                            @endif
                            @if ($alasanKey)<p class="dm-alasan">{{ $d[$alasanKey] }}</p>@endif
                            @if ($sisa->count())
                                <div class="dm-meta">
                                    @foreach ($sisa as $key => $value)
                                        <span>{{ ucwords(str_replace('_', ' ', $key)) }}: <b>{{ $value }}</b></span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="dm-keputusan" style="margin-top:12px;padding-top:12px;border-top:1px solid var(--line);">
                                <p style="font-size:12px;margin:0 0 8px;">Keputusan dosen PA: <b>{{ ucfirst($rek->status) }}</b></p>
                                <form method="POST" action="{{ route('dosen.rekomendasi.keputusan', $rek->id) }}">
                                    @csrf
                                    <textarea name="catatan" rows="2" maxlength="500" placeholder="Catatan (wajib untuk Revisi atau Tolak)" style="width:100%;margin-bottom:8px;"></textarea>
                                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                        <button type="submit" name="status" value="disetujui" class="ds-btn sm">Setujui</button>
                                        <button type="submit" name="status" value="revisi" class="ds-btn sm">Revisi</button>
                                        <button type="submit" name="status" value="ditolak" class="ds-btn sm">Tolak</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="dm-empty">Belum ada rekomendasi ARAH.</div>
            @endif
        </section>

    </div>
</div>

@endsection