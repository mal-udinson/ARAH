@extends('layouts.app')

@section('content')

<div class="ac-page">

    <div class="ac-bg" aria-hidden="true">
        <i class="ac-arrow" style="--x:6%;  --s:20px; --d:18s; --delay:-3s"></i>
        <i class="ac-arrow" style="--x:30%; --s:14px; --d:22s; --delay:-10s"></i>
        <i class="ac-arrow" style="--x:68%; --s:24px; --d:19s; --delay:-6s"></i>
        <i class="ac-arrow" style="--x:92%; --s:16px; --d:24s; --delay:-14s"></i>
    </div>

    <div class="ac-wrap">

        {{-- HERO --}}
        <header class="ac-hero">
            <div>
                <span class="ac-chip">ACADEMIC DATA</span>
                <h1>Data Akademik</h1>
                <p>
                    Kelola riwayat nilai kamu untuk membantu ARAH
                    membaca kemampuan akademik dengan lebih akurat.
                </p>
            </div>
            <img class="ac-mascot" src="{{ asset('images/nyapa.png') }}" alt="Maskot NARA">
        </header>

        {{-- RINGKASAN --}}
        <div class="ac-stats">
            <div class="ac-stat">
                <span class="ac-sicon"><svg class="ac-ico" viewBox="0 0 24 24"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg></span>
                <div class="ac-slabel">Rata-rata nilai</div>
                <div class="ac-svalue">{{ $rataNilai !== null ? number_format($rataNilai, 2) : '—' }}</div>
                <div class="ac-scap">Dihitung dari seluruh nilai angka</div>
            </div>
            <div class="ac-stat">
                <span class="ac-sicon"><svg class="ac-ico" viewBox="0 0 24 24"><path d="M4 5a2 2 0 0 1 2-2h13v16H6a2 2 0 0 0-2 2z"/><path d="M4 19V5M9 7h6"/></svg></span>
                <div class="ac-slabel">Mata kuliah tercatat</div>
                <div class="ac-svalue">{{ $nilai->count() }}</div>
                <div class="ac-scap">Sudah masuk profil akademikmu</div>
            </div>
            <div class="ac-stat">
                <span class="ac-sicon"><svg class="ac-ico" viewBox="0 0 24 24"><path d="M12 3l9 5-9 5-9-5z"/><path d="M3 13l9 5 9-5"/></svg></span>
                <div class="ac-slabel">Total SKS</div>
                <div class="ac-svalue">{{ $nilai->sum(fn ($n) => $n->mataKuliah->sks ?? 0) }}</div>
                <div class="ac-scap">Dari mata kuliah yang tercatat</div>
            </div>
        </div>

        {{-- SUCCESS --}}
        @if(session('success'))
            <div class="ac-alert">
                <svg class="ac-ico" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- INPUT --}}
        <section class="ac-card">
            <div class="ac-label">INPUT NILAI</div>
            <h2>Tambahkan nilai mata kuliah</h2>

            <form method="POST" action="{{ route('academic.store') }}" class="ac-form">
                @csrf

                <div class="ac-field">
                    <div class="ac-flabel">Mata Kuliah</div>
                    <div class="ac-dd" data-dd data-search="Cari kode atau nama mata kuliah…">
                        <select id="mata_kuliah_id" name="mata_kuliah_id" class="ac-native" required tabindex="-1" data-msg="Pilih mata kuliah dulu.">
                            <option value="">Pilih mata kuliah</option>
                            @foreach($mataKuliah as $mk)
                                <option value="{{ $mk->id }}" data-kode="{{ $mk->kode }}" data-nama="{{ $mk->nama }}" data-sks="{{ $mk->sks }}"
                                    {{ old('mata_kuliah_id') == $mk->id ? 'selected' : '' }}>{{ $mk->kode }} — {{ $mk->nama }} ({{ $mk->sks }} SKS)</option>
                            @endforeach
                        </select>
                    </div>
                    <noscript><style>.ac-native { position: static; opacity: 1; height: auto; pointer-events: auto; }</style></noscript>
                    @error('mata_kuliah_id')
                        <div class="ac-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="ac-field">
                    <label for="nilai_angka">Nilai Angka</label>
                    <input id="nilai_angka" type="number" name="nilai_angka" class="ac-input"
                           min="0" max="100" step="0.01" value="{{ old('nilai_angka') }}"
                           placeholder="Contoh: 85" required data-msg="Isi nilai angka antara 0 sampai 100.">
                    @error('nilai_angka')
                        <div class="ac-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="ac-field wide">
                    <div class="ac-flabel">Nilai Huruf</div>
                    <div class="ac-chips">
                        @foreach(['A', 'B', 'C', 'D', 'E'] as $h)
                            <label class="ac-chipopt">
                                <input type="radio" name="nilai_huruf" value="{{ $h }}"
                                       {{ old('nilai_huruf') == $h ? 'checked' : '' }}
                                       {{ $loop->first ? 'required' : '' }} data-msg="Pilih nilai huruf.">
                                <span>{{ $h }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('nilai_huruf')
                        <div class="ac-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="ac-actions">
                    <button type="submit" class="ac-btn">Simpan nilai →</button>
                </div>
            </form>
        </section>

        {{-- RIWAYAT --}}
        <section class="ac-card">
            <div class="ac-label">RIWAYAT NILAI</div>
            <h2>Nilai kamu</h2>

            @if($nilai->isEmpty())

                <div class="ac-empty">
                    <svg class="ac-hex" viewBox="0 0 64 64" fill="none" stroke="#a5b4fc" stroke-width="1.6">
                        <path d="M32 4l24 14v28L32 60 8 46V18z"/>
                        <path d="M32 16l14 8v16l-14 8-14-8V24z" stroke="#7C3AED"/>
                        <circle cx="32" cy="32" r="2.5" fill="#7C3AED" stroke="none"/>
                    </svg>
                    <strong>Belum ada nilai</strong>
                    <p>Masukkan nilai mata kuliah kamu untuk mulai membentuk profil akademik.</p>
                </div>

            @else

                <div class="ac-table-wrap">
                    <table class="ac-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Mata Kuliah</th>
                                <th>SKS</th>
                                <th>Nilai</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($nilai as $item)
                                @php
                                    $h = $item->nilai_huruf;
                                    $tone = in_array($h, ['A', 'AB']) ? 'g' : (in_array($h, ['B', 'BC']) ? 'i' : ($h === 'C' ? 'a' : 'r'));
                                @endphp
                                <tr>
                                    <td><span class="ac-code">{{ $item->mataKuliah->kode }}</span></td>
                                    <td class="ac-course">{{ $item->mataKuliah->nama }}</td>
                                    <td><span class="ac-sks">{{ $item->mataKuliah->sks }} SKS</span></td>
                                    <td>
                                        <div class="ac-grade">
                                            <strong>{{ number_format($item->nilai_angka, 2) }}</strong>
                                            <span class="ac-badge {{ $tone }}">{{ $h }}</span>
                                        </div>
                                    </td>
                                    <td class="ac-right">
                                        <form method="POST" action="{{ route('academic.destroy', $item) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ac-del" onclick="return confirm('Hapus nilai ini?')">
                                                <svg class="ac-ico" viewBox="0 0 24 24"><path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14"/></svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="ac-info">
                    Rata-rata dihitung otomatis berdasarkan seluruh nilai angka yang tersimpan.
                </div>

            @endif
        </section>

    </div>
</div>


<style>
    .ac-page {
        position: relative; overflow: hidden; min-height: calc(100vh - 70px);
        padding: 52px 22px 70px; font-family: 'Poppins', sans-serif;
        background: linear-gradient(165deg, #1b1e6b 0%, #2c2b9b 50%, #6d35d9 100%);
    }
    .ac-page::before {
        content: ""; position: absolute; inset: 0; pointer-events: none;
        background:
            radial-gradient(700px 420px at 10% 6%, rgba(124, 58, 237, .45), transparent 65%),
            linear-gradient(rgba(255, 255, 255, .045) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, .045) 1px, transparent 1px);
        background-size: auto, 56px 56px, 56px 56px;
        animation: ac-grid 40s linear infinite;
    }
    @keyframes ac-grid { to { background-position: 0 0, 0 56px, 56px 0; } }
    .ac-bg { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
    .ac-arrow { position: absolute; bottom: -50px; left: var(--x); width: var(--s); height: var(--s); opacity: 0; animation: ac-rise var(--d) linear infinite; animation-delay: var(--delay); }
    .ac-arrow::before { content: ""; position: absolute; inset: 0; border-radius: 3px; border-top: 2.5px solid rgba(255, 255, 255, .5); border-left: 2.5px solid rgba(255, 255, 255, .5); transform: rotate(45deg) scale(.7); }
    @keyframes ac-rise { 0% { transform: translateY(0); opacity: 0; } 12%, 88% { opacity: .4; } 100% { transform: translateY(-110vh) rotate(12deg); opacity: 0; } }

    .ac-page *, .ac-page *::before, .ac-page *::after { box-sizing: border-box; }
    .ac-field { min-width: 0; }
    .ac-wrap { position: relative; z-index: 1; width: min(1150px, 100%); margin: 0 auto; }
    .ac-ico { width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }

    /* Hero */
    .ac-hero { display: flex; align-items: center; justify-content: space-between; gap: 24px; margin-bottom: 30px; }
    .ac-chip { display: inline-block; padding: 5px 12px; border-radius: 99px; background: rgba(255, 255, 255, .14); border: 1px solid rgba(255, 255, 255, .22); color: #fff; font-size: 10px; font-weight: 600; letter-spacing: .12em; }
    .ac-hero h1 { margin: 14px 0 10px; color: #fff; font-size: 44px; line-height: 1.1; font-weight: 700; letter-spacing: -.03em; }
    .ac-hero p { max-width: 560px; margin: 0; color: rgba(255, 255, 255, .85); font-size: 15px; line-height: 1.75; }
    .ac-mascot { width: 170px; height: auto; flex-shrink: 0; filter: drop-shadow(0 18px 26px rgba(10, 10, 60, .4)); animation: ac-bob 4s ease-in-out infinite; }
    @keyframes ac-bob { 0%, 100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-10px) rotate(2deg); } }

    /* Ringkasan */
    .ac-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-bottom: 22px; }
    .ac-stat { padding: 22px 24px; border-radius: 26px; background: #fff; box-shadow: 0 18px 44px rgba(10, 10, 70, .28); }
    .ac-sicon { width: 44px; height: 44px; display: grid; place-items: center; margin-bottom: 14px; border-radius: 14px; background: #eef2ff; color: #4F46E5; }
    .ac-slabel { color: #64748b; font-size: 13px; font-weight: 500; }
    .ac-svalue { margin: 4px 0; color: #1E2A6B; font-size: 34px; line-height: 1.1; font-weight: 700; letter-spacing: -.02em; }
    .ac-scap { color: #94a3b8; font-size: 12px; }

    .ac-alert { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; padding: 13px 18px; border-radius: 16px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; font-size: 13px; font-weight: 500; }

    /* Kartu */
    .ac-card { margin-bottom: 22px; padding: 30px 32px; border-radius: 28px; background: #fff; box-shadow: 0 18px 44px rgba(10, 10, 70, .28); }
    .ac-label { color: #4F46E5; font-size: 11px; font-weight: 600; letter-spacing: .12em; }
    .ac-card h2 { margin: 6px 0 22px; color: #1E2A6B; font-size: 22px; font-weight: 700; letter-spacing: -.02em; }

    /* Form */
    .ac-form { display: grid; grid-template-columns: minmax(0, 1.8fr) minmax(0, .8fr); align-items: start; gap: 18px; }
    .ac-field > label, .ac-flabel { display: block; margin-bottom: 7px; color: #475569; font-size: 12px; font-weight: 600; }
    .ac-input {
        width: 100%; height: 50px; padding: 0 16px; border: 1.5px solid #e2e8f0; border-radius: 14px; outline: none;
        background: #f8fafc; color: #1E2A6B; font: inherit; font-size: 13.5px; transition: all .2s ease;
    }
    select.ac-input {
        appearance: none; -webkit-appearance: none; padding-right: 42px; cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 16px center;
    }
    .ac-input::placeholder { color: #94a3b8; }
    .ac-input:hover { border-color: #cbd5e1; }
    .ac-input:focus { border-color: #4F46E5; background-color: #fff; box-shadow: 0 0 0 4px rgba(79, 70, 229, .14); }
    .ac-error { margin-top: 6px; color: #dc2626; font-size: 11px; }
    .ac-btn {
        height: 50px; padding: 0 26px; border: 0; border-radius: 16px; white-space: nowrap; cursor: pointer;
        background: linear-gradient(135deg, #1E2A6B, #4F46E5); color: #fff; font: inherit; font-size: 14px; font-weight: 600;
        box-shadow: 0 12px 26px rgba(79, 70, 229, .35); transition: all .2s ease;
    }
    .ac-btn:hover { transform: translateY(-1px); box-shadow: 0 16px 32px rgba(79, 70, 229, .45); }
    .ac-btn:active { transform: none; }

    /* Tabel */
    .ac-table-wrap { overflow-x: auto; }
    .ac-table { width: 100%; border-collapse: collapse; }
    .ac-table th { padding: 12px 12px; color: #94a3b8; border-bottom: 1px solid #eef2f7; font-size: 11px; font-weight: 600; text-align: left; text-transform: uppercase; letter-spacing: .08em; }
    .ac-table td { padding: 16px 12px; border-bottom: 1px solid #f1f5f9; color: #475569; font-size: 14px; vertical-align: middle; }
    .ac-table tbody tr { transition: background .15s ease; }
    .ac-table tbody tr:hover { background: #faf9ff; }
    .ac-right { text-align: right; }
    .ac-code { padding: 4px 10px; border-radius: 8px; background: #eef2ff; color: #4F46E5; font-size: 12px; font-weight: 600; }
    .ac-course { color: #1E2A6B; font-weight: 600; }
    .ac-sks { color: #64748b; font-size: 13px; }
    .ac-grade { display: flex; align-items: center; gap: 10px; }
    .ac-grade strong { color: #1E2A6B; font-size: 15px; font-weight: 700; }
    .ac-badge { padding: 3px 11px; border-radius: 99px; font-size: 12px; font-weight: 700; }
    .ac-badge.g { background: #d1fae5; color: #047857; }
    .ac-badge.i { background: #e8e6ff; color: #4F46E5; }
    .ac-badge.a { background: #fef3c7; color: #b45309; }
    .ac-badge.r { background: #fee2e2; color: #b91c1c; }
    .ac-del { display: inline-flex; align-items: center; gap: 6px; padding: 8px 12px; border: 0; border-radius: 10px; background: transparent; color: #ef4444; font: inherit; font-size: 12px; font-weight: 500; cursor: pointer; transition: .2s ease; }
    .ac-del .ac-ico { width: 15px; height: 15px; }
    .ac-del:hover { background: #fef2f2; color: #b91c1c; }
    .ac-info { margin-top: 18px; color: #94a3b8; font-size: 12px; line-height: 1.6; }

    /* Kosong */
    .ac-empty { padding: 34px 20px; text-align: center; border: 1.5px dashed #ddd9ff; border-radius: 22px; background: #faf9ff; }
    .ac-hex { width: 74px; height: 74px; margin-bottom: 8px; }
    .ac-empty strong { display: block; color: #1E2A6B; font-size: 17px; font-weight: 700; }
    .ac-empty p { max-width: 420px; margin: 6px auto 0; color: #64748b; font-size: 13px; line-height: 1.7; }


    /* ===== Komponen kustom: dropdown pencarian, chip nilai, angka ===== */
    .ac-input[type=number] { -moz-appearance: textfield; appearance: textfield; }
    .ac-input[type=number]::-webkit-inner-spin-button, .ac-input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    .ac-field.wide, .ac-actions { grid-column: 1 / -1; }
    .ac-actions { display: flex; justify-content: flex-end; }

    .ac-dd { position: relative; }
    .ac-native { position: absolute; left: 0; bottom: 0; width: 100%; height: 1px; opacity: 0; pointer-events: none; }
    .ac-dd-btn { display: flex; align-items: center; justify-content: space-between; gap: 10px; text-align: left; cursor: pointer; color: #94a3b8; }
    .ac-dd-btn.has-val { color: #1E2A6B; }
    .ac-dd-text { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ac-dd-chev { color: #64748b; transition: transform .2s ease; }
    .ac-dd.open .ac-dd-chev { transform: rotate(180deg); }
    .ac-dd.open .ac-dd-btn { border-color: #4F46E5; background: #fff; box-shadow: 0 0 0 4px rgba(79, 70, 229, .14); }
    .ac-dd-panel { position: absolute; z-index: 20; top: calc(100% + 8px); left: 0; right: 0; display: none; padding: 10px; border-radius: 18px; background: #fff; border: 1px solid #e5e3ff; box-shadow: 0 24px 60px rgba(20, 20, 90, .28); }
    .ac-dd.open .ac-dd-panel { display: block; animation: ac-pop .15s ease; }
    @keyframes ac-pop { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: none; } }
    .ac-dd-panel [hidden] { display: none !important; }
    .ac-dd-search { width: 100%; height: 42px; margin-bottom: 8px; padding: 0 14px; border: 1.5px solid #e2e8f0; border-radius: 12px; outline: none; background: #f8fafc; color: #1E2A6B; font: inherit; font-size: 13px; }
    .ac-dd-search:focus { border-color: #4F46E5; background: #fff; }
    .ac-dd-list { max-height: 280px; margin: 0; padding: 0; overflow-y: auto; list-style: none; scrollbar-width: thin; scrollbar-color: #c7c2ff transparent; }
    .ac-dd-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 12px; color: #334155; font-size: 13px; cursor: pointer; transition: background .15s ease; }
    .ac-dd-item:hover { background: #f3f2ff; }
    .ac-dd-item.on { background: #e8e6ff; color: #1E2A6B; font-weight: 600; }
    .ac-dd-code { flex-shrink: 0; padding: 3px 9px; border-radius: 8px; background: #eef2ff; color: #4F46E5; font-size: 11px; font-weight: 600; }
    .ac-dd-name { flex: 1; min-width: 0; }
    .ac-dd-sks { flex-shrink: 0; color: #94a3b8; font-size: 11px; }
    .ac-dd-empty { padding: 14px; text-align: center; color: #94a3b8; font-size: 12px; }

    .ac-chips { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 12px; max-width: 640px; }
    .ac-chipopt { position: relative; cursor: pointer; }
    .ac-chipopt input { position: absolute; opacity: 0; pointer-events: none; }
    .ac-chipopt span { display: grid; place-items: center; width: 100%; height: 46px; padding: 0 12px; user-select: none; border: 1.5px solid #e2e8f0; border-radius: 14px; background: #f8fafc; color: #475569; font-size: 14px; font-weight: 700; transition: all .2s ease; }
    .ac-chipopt:hover span { border-color: #a5b4fc; color: #4F46E5; transform: translateY(-1px); }
    .ac-chipopt input:checked + span { border-color: transparent; background: linear-gradient(135deg, #1E2A6B, #4F46E5); color: #fff; box-shadow: 0 10px 22px rgba(79, 70, 229, .35); }
    .ac-chipopt input:focus-visible + span { box-shadow: 0 0 0 4px rgba(79, 70, 229, .25); }

    @media (prefers-reduced-motion: reduce) { .ac-page::before, .ac-arrow, .ac-mascot { animation: none !important; } }
    @media (max-width: 900px) {
        .ac-form { grid-template-columns: 1fr; }
        .ac-stats { grid-template-columns: 1fr; }
    }
    @media (max-width: 560px) {
        .ac-page { padding: 32px 14px 50px; }
        .ac-hero h1 { font-size: 32px; }
        .ac-mascot { display: none; }
        .ac-card { padding: 24px 20px; border-radius: 22px; }
        .ac-form { grid-template-columns: 1fr; }
        .ac-actions, .ac-btn { width: 100%; }
    }
</style>


<script>
    (function () {
        function el(tag, cls, text) { var e = document.createElement(tag); if (cls) e.className = cls; if (text != null) e.textContent = text; return e; }

        // Dropdown kustom (dengan pencarian) yang tersinkron ke <select> asli
        document.querySelectorAll('[data-dd]').forEach(function (dd) {
            var sel = dd.querySelector('select'), btn = el('button', 'ac-input ac-dd-btn'), txt = el('span', 'ac-dd-text'),
                panel = el('div', 'ac-dd-panel'), list = el('ul', 'ac-dd-list'), search = null, items = [];
            btn.type = 'button';
            btn.appendChild(txt);
            btn.insertAdjacentHTML('beforeend', '<svg class="ac-ico ac-dd-chev" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>');
            if (dd.dataset.search) { search = el('input', 'ac-dd-search'); search.type = 'text'; search.placeholder = dd.dataset.search; panel.appendChild(search); }
            panel.appendChild(list); dd.appendChild(btn); dd.appendChild(panel);

            Array.prototype.forEach.call(sel.options, function (o) {
                if (!o.value) return;
                var li = el('li', 'ac-dd-item');
                if (o.dataset.kode) {
                    li.appendChild(el('span', 'ac-dd-code', o.dataset.kode));
                    li.appendChild(el('span', 'ac-dd-name', o.dataset.nama));
                    li.appendChild(el('span', 'ac-dd-sks', o.dataset.sks + ' SKS'));
                } else { li.textContent = o.textContent.trim(); }
                li.dataset.v = o.value; li.dataset.q = o.textContent.toLowerCase();
                li.addEventListener('click', function () { sel.value = o.value; sel.dispatchEvent(new Event('change', { bubbles: true })); close(); });
                list.appendChild(li); items.push(li);
            });
            var empty = el('li', 'ac-dd-empty', 'Tidak ada yang cocok'); empty.hidden = true; list.appendChild(empty);

            function sync() { txt.textContent = sel.options[sel.selectedIndex].textContent.trim(); btn.classList.toggle('has-val', !!sel.value); items.forEach(function (i) { i.classList.toggle('on', i.dataset.v === sel.value); }); }
            function filter() { var q = search ? search.value.toLowerCase().trim() : '', n = 0; items.forEach(function (i) { var ok = !q || i.dataset.q.indexOf(q) > -1; i.hidden = !ok; if (ok) n++; }); empty.hidden = n > 0; }
            function open() { dd.classList.add('open'); if (search) { search.value = ''; filter(); search.focus(); } }
            function close() { dd.classList.remove('open'); }

            btn.addEventListener('click', function () { dd.classList.contains('open') ? close() : open(); });
            if (search) search.addEventListener('input', filter);
            sel.addEventListener('change', sync);
            sel.addEventListener('focus', open);   // saat validasi memfokuskan field yang kosong
            document.addEventListener('click', function (e) { if (!dd.contains(e.target)) close(); });
            dd.addEventListener('keydown', function (e) { if (e.key === 'Escape') { close(); btn.focus(); } });
            sync();
        });

        // Pesan validasi berbahasa Indonesia
        document.querySelectorAll('[data-msg]').forEach(function (f) {
            f.addEventListener('invalid', function () { f.setCustomValidity(f.dataset.msg); });
            ['input', 'change'].forEach(function (ev) { f.addEventListener(ev, function () { f.setCustomValidity(''); }); });
        });
    })();
</script>

@endsection