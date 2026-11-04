@extends('layouts.app')

@section('title', 'Asesmen minat — ARAH')

@section('content')

@php
    $labels = [
        1 => 'Sangat tidak setuju',
        2 => 'Tidak setuju',
        3 => 'Netral',
        4 => 'Setuju',
        5 => 'Sangat setuju',
    ];

    $n = 0;
@endphp

<style>
    .as { --ink:#1E2A6B; --pri:#4F46E5; --vio:#7C3AED; position: relative; overflow: hidden; min-height: calc(100vh - 64px); padding: 32px 22px 56px; color: #fff;
        font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; background: linear-gradient(165deg, #1b1760 0%, #3a2ea6 50%, #6b35e0 100%); }
    .as *, .as *::before, .as *::after { box-sizing: border-box; }
    :where(.as) h1, :where(.as) p { margin: 0; }
    .as-bg { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
    .as-o1, .as-o2 { position: absolute; border-radius: 50%; filter: blur(80px); animation: as-dr 28s ease-in-out infinite alternate; }
    .as-o1 { width: 460px; height: 460px; top: -150px; left: -120px; background: #8b5cf6; opacity: .5; }
    .as-o2 { width: 400px; height: 400px; bottom: -140px; right: -120px; background: #c026d3; opacity: .26; animation-duration: 32s; }
    @keyframes as-dr { to { transform: translate(100px, 70px) scale(1.1); } }
    .as-grid { position: absolute; inset: 0; background-size: 56px 56px; -webkit-mask-image: linear-gradient(#000 20%, transparent 95%); mask-image: linear-gradient(#000 20%, transparent 95%);
        background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px); }
    .as-wrap { position: relative; z-index: 1; width: min(860px, 100%); margin: 0 auto; }

    .as-head { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 20px; }
    .as-head h1 { font-size: clamp(26px, 4vw, 38px); line-height: 1.15; font-weight: 700; letter-spacing: -.03em; }
    .as-head p { max-width: 460px; margin-top: 8px; color: rgba(255,255,255,.8); font-size: 13.5px; line-height: 1.75; }
    .as-head img { flex-shrink: 0; width: 150px; height: 150px; object-fit: contain; filter: drop-shadow(0 18px 28px rgba(10,6,60,.45)) drop-shadow(0 0 28px rgba(196,181,253,.35)); animation: as-bob 4s ease-in-out infinite; }
    @keyframes as-bob { 0%, 100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-8px) rotate(2deg); } }

    .as-pt { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
    .as-pt b { font-size: 14px; font-weight: 700; }
    .as-pt span { color: rgba(255,255,255,.75); font-size: 12px; text-align: right; }
    .as-seg { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-bottom: 22px; }
    .as-gn { margin-bottom: 6px; color: rgba(255,255,255,.7); font-size: 11px; font-weight: 500; }
    .as-bars { display: grid; grid-template-columns: repeat(4, 1fr); gap: 3px; }
    .as-bars i { height: 6px; border-radius: 99px; background: rgba(255,255,255,.2); transition: background .3s, transform .3s; }
    .as-bars i.done { background: #c4b5fd; }
    .as-bars i.cur { background: #fff; transform: scaleY(1.5); }

    .as-card { padding: 30px; color: var(--ink); background: #fff; border-radius: 24px; box-shadow: 0 24px 60px rgba(10,6,60,.35); animation: as-in .35s ease backwards; }
    .as-card[hidden] { display: none; }
    @keyframes as-in { from { opacity: 0; transform: translateY(10px); } }
    .as-q { font-size: clamp(17px, 2.4vw, 21px); line-height: 1.55; font-weight: 600; letter-spacing: -.01em; }
    .as-scale { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-top: 26px; }
    .as-opt { position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 9px; min-height: 96px; padding: 12px 6px; text-align: center; background: #f8f7ff; border: 1.5px solid #e5e3ff; border-radius: 16px; cursor: pointer; transition: transform .18s, border-color .18s, box-shadow .18s; }
    .as-opt input { position: absolute; opacity: 0; pointer-events: none; }
    .as-opt:hover { transform: translateY(-3px); border-color: #c4b5fd; }
    .as-n { width: 34px; height: 34px; display: grid; place-items: center; border-radius: 50%; background: #fff; border: 1.5px solid #e5e3ff; color: #5b5bd6; font-size: 13px; font-weight: 700; }
    .as-l { color: #64748b; font-size: 11px; line-height: 1.35; }
    .as-opt:has(input:checked) { background: linear-gradient(160deg, var(--pri), var(--vio)); border-color: transparent; box-shadow: 0 12px 26px rgba(79,70,229,.35); }
    .as-opt:has(input:checked) .as-n { color: var(--pri); border-color: #fff; }
    .as-opt:has(input:checked) .as-l { color: #fff; font-weight: 600; }
    .as-opt:has(input:focus-visible) { outline: 3px solid #c4b5fd; outline-offset: 2px; }
    .as-err { margin-top: 14px; color: #dc2626; font-size: 12px; font-weight: 500; }

    .as-act { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 24px; }
    .as-b { min-height: 44px; padding: 0 22px; border: 0; border-radius: 13px; font: 600 13px 'Poppins', sans-serif; cursor: pointer; transition: transform .2s; }
    .as-b:hover { transform: translateY(-2px); }
    .as-b:focus-visible { outline: 3px solid #c4b5fd; outline-offset: 2px; }
    .as-prev { color: #5b5bd6; background: #eef2ff; }
    .as-next { color: #fff; background: linear-gradient(135deg, var(--ink), var(--pri)); box-shadow: 0 10px 24px rgba(30,42,107,.3); }

    @media (max-width: 700px) {
        .as { padding: 20px 12px 40px; }
        .as-head img { width: 84px; height: 84px; }
        .as-gn { display: none; }
        .as-seg { gap: 6px; }
        .as-pt { flex-direction: column; gap: 2px; }
        .as-pt span { text-align: left; }
        .as-card { padding: 22px 16px; border-radius: 20px; }
        .as-scale { gap: 6px; }
        .as-opt { min-height: 80px; padding: 8px 2px; }
        .as-n { width: 30px; height: 30px; }
        .as-l { font-size: 9.5px; }
    }
    @media (prefers-reduced-motion: reduce) { .as *, .as *::before { animation: none !important; transition: none !important; } }
</style>

<div class="as">
    <div class="as-bg" aria-hidden="true"><i class="as-o1"></i><i class="as-o2"></i><div class="as-grid"></div></div>

    <div class="as-wrap">
        <header class="as-head">
            <div>
                <h1>Kenali minat akademikmu</h1>
                <p>Jawab sesuai kondisi dan ketertarikanmu yang sebenarnya. Tidak ada jawaban benar atau salah.</p>
            </div>
            <img src="{{ asset('images/happy.png') }}" alt="NARA">
        </header>

        <section aria-live="polite">
            <div class="as-pt"><b id="asNo">Pertanyaan 1 dari 20</b><span id="asCat">{{ $groups[0][1] }}</span></div>
            <div class="as-seg">
                @php $q = 0; @endphp
                @foreach ($groups as $g)
                    <div>
                        <div class="as-gn">{{ $g[0] }}</div>
                        <div class="as-bars">@foreach ($g[2] as $_)@php $q++; @endphp<i data-s="{{ $q }}"></i>@endforeach</div>
                    </div>
                @endforeach
            </div>
        </section>

        <form id="assessmentForm" method="POST" action="{{ route('assessment.store') }}" novalidate>
            @csrf
            <input
    type="hidden"
    name="assessment_payload"
    value="{{ $assessmentPayload }}"
>
            @foreach ($groups as $g)
                @foreach ($g[2] as $text)
                    @php $n++; @endphp
                    <div class="as-card" data-q="{{ $n }}" data-cat="{{ $g[1] }}" @if ($n > 1) hidden @endif>
                        <p class="as-q">{{ $text }}</p>
                        <div class="as-scale" role="radiogroup" aria-label="Pertanyaan {{ $n }}">
                            @for ($s = 1; $s <= 5; $s++)
                                <label class="as-opt">
                                    <input type="radio" name="q{{ $n }}" value="{{ $s }}">
                                    <span class="as-n">{{ $s }}</span>
                                    <span class="as-l">{{ $labels[$s] }}</span>
                                </label>
                            @endfor
                        </div>
                        <p class="as-err" role="alert" hidden>Pilih salah satu jawaban dulu.</p>
                        <div class="as-act">
                            <button type="button" class="as-b as-prev" data-prev @if ($n === 1) style="visibility:hidden" @endif>Kembali</button>
                            @if ($n < 20)
                                <button type="button" class="as-b as-next" data-next>Lanjut</button>
                            @else
                                <button type="submit" class="as-b as-next">Selesai dan analisis</button>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endforeach
        </form>
    </div>
</div>

<script>
(function () {
    var form = document.getElementById('assessmentForm');
    var cards = [].slice.call(form.querySelectorAll('.as-card'));
    var segs = document.querySelectorAll('.as-bars i');
    var no = document.getElementById('asNo'), cat = document.getElementById('asCat');
    var total = cards.length, cur = 1;

    function answered(n) { return !!form.querySelector('input[name="q' + n + '"]:checked'); }
    function err(n, on) { cards[n - 1].querySelector('.as-err').hidden = !on; }
    function paint() {
        segs.forEach(function (s) { var n = +s.dataset.s; s.className = (answered(n) ? 'done ' : '') + (n === cur ? 'cur' : ''); });
        no.textContent = 'Pertanyaan ' + cur + ' dari ' + total;
        cat.textContent = cards[cur - 1].dataset.cat;
    }
    function show(n) {
        cards.forEach(function (c) { c.hidden = +c.dataset.q !== n; });
        cur = n;
        paint();
    }

    form.addEventListener('click', function (e) {
        if (e.target.closest('[data-next]')) {
            if (!answered(cur)) { err(cur, true); return; }
            if (cur < total) show(cur + 1);
        } else if (e.target.closest('[data-prev]') && cur > 1) {
            show(cur - 1);
        }
    });

    // Pilih jawaban, lalu otomatis lanjut ke pertanyaan berikutnya
    form.addEventListener('change', function (e) {
        if (e.target.type !== 'radio') return;
        var at = cur;
        err(at, false);
        paint();
        if (at < total) setTimeout(function () { if (cur === at) show(at + 1); }, 280);
    });

    form.addEventListener('submit', function (e) {
        for (var i = 1; i <= total; i++) {
            if (!answered(i)) { e.preventDefault(); show(i); err(i, true); return; }
        }
    });

    show(1);
})();
</script>

@endsection