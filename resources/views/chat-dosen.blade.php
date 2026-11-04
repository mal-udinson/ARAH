@extends('layouts.app')

@section('title', 'Chat Dosen — ARAH')

@section('content')

@php
    // Aman untuk mahasiswa yang BELUM punya Dosen PA ($dosen = null)
    $adaDosen  = ! empty($dosen);
    $namaDosen = $adaDosen ? $dosen->nama : 'Dosen PA';
    $nipDosen  = $adaDosen ? ($dosen->nip ?? null) : null;
    $inisial   = strtoupper(mb_substr($namaDosen, 0, 1));
    $messages  = $messages ?? collect();
    $lastDay   = null;
    $saran     = ['Saya ingin berkonsultasi tentang rekomendasi mata kuliah.', 'Apakah Bapak/Ibu ada waktu untuk bimbingan minggu ini?', 'Mohon masukan untuk rencana studi semester depan.'];
@endphp

<style>
    .cd-page { position: relative; overflow: hidden; min-height: calc(100dvh - 64px); padding: 22px; font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
        background: linear-gradient(165deg, #14235f 0%, #3a3db5 52%, #6d35d9 100%); }
    .cd-page *, .cd-page *::before, .cd-page *::after { box-sizing: border-box; }
    .cd-bg { position: absolute; inset: 0; overflow: hidden; pointer-events: none; }
    .cd-orb { position: absolute; border-radius: 50%; filter: blur(80px); animation: cd-dr 24s ease-in-out infinite alternate; }
    .cd-orb.one { width: 480px; height: 480px; top: -160px; left: -140px; background: #6366f1; opacity: .5; }
    .cd-orb.two { width: 420px; height: 420px; right: -130px; bottom: -150px; background: #a855f7; opacity: .42; animation-direction: alternate-reverse; }
    @keyframes cd-dr { to { transform: translate(120px, 90px) scale(1.15); } }
    .cd-grid { position: absolute; inset: 0; background-size: 56px 56px; animation: cd-gm 40s linear infinite;
        background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
        -webkit-mask-image: linear-gradient(#000 20%, transparent 95%); mask-image: linear-gradient(#000 20%, transparent 95%); }
    @keyframes cd-gm { to { background-position: 0 56px, 56px 0; } }
    .cd-arw { position: absolute; bottom: -40px; left: var(--x); width: var(--s); height: var(--s); opacity: 0; animation: cd-up var(--d) linear infinite; animation-delay: var(--dl); }
    .cd-arw::before { content: ""; position: absolute; inset: 0; border-radius: 3px; border-top: 2.5px solid rgba(255,255,255,.6); border-left: 2.5px solid rgba(255,255,255,.6); transform: rotate(45deg) scale(.7); }
    @keyframes cd-up { 0% { transform: translateY(0); opacity: 0; } 12%, 88% { opacity: .4; } 100% { transform: translateY(-115vh); opacity: 0; } }

    /* KARTU CHAT */
    .cd-wrap { position: relative; z-index: 2; width: min(100%, 1080px); height: calc(100dvh - 118px); min-height: 540px; margin: 0 auto; display: flex; flex-direction: column; overflow: hidden;
        border: 1px solid rgba(255,255,255,.7); border-radius: 28px; background: #fff; box-shadow: 0 28px 70px rgba(8,16,70,.38); animation: cd-in .6s ease both; }
    @keyframes cd-in { from { opacity: 0; transform: translateY(16px); } }

    /* HEADER */
    .cd-header { flex-shrink: 0; display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 16px 22px; border-bottom: 1px solid #eeeaf6; background: linear-gradient(180deg, #faf9ff, #fff); }
    .cd-left { display: flex; align-items: center; gap: 14px; min-width: 0; }
    .cd-back { width: 40px; height: 40px; flex-shrink: 0; display: grid; place-items: center; border: 1px solid #e5e0f5; border-radius: 12px; color: #6b6475; background: #fff; text-decoration: none; transition: .2s ease; }
    .cd-back:hover { color: #4F46E5; background: #f3f2ff; border-color: #cfc9fb; transform: translateX(-2px); }
    .cd-back svg { width: 19px; height: 19px; }
    .cd-avatar { position: relative; width: 46px; height: 46px; flex-shrink: 0; display: grid; place-items: center; border-radius: 15px; color: #fff; font-size: 17px; font-weight: 700; background: linear-gradient(135deg, #4F46E5, #7C3AED); box-shadow: 0 8px 18px rgba(79,70,229,.28); }
    .cd-avatar.off { background: linear-gradient(135deg, #cbd5e1, #94a3b8); box-shadow: none; }
    .cd-avatar::after { content: ""; position: absolute; right: -3px; bottom: -3px; width: 13px; height: 13px; border-radius: 50%; background: #22c55e; border: 2.5px solid #fff; }
    .cd-avatar.off::after { background: #cbd5e1; }
    .cd-user { min-width: 0; }
    .cd-name { margin: 0; overflow: hidden; color: #1E2A6B; font-size: 15.5px; font-weight: 700; text-overflow: ellipsis; white-space: nowrap; }
    .cd-meta { margin-top: 3px; color: #8b8495; font-size: 11.5px; }
    .cd-pill { display: inline-flex; align-items: center; gap: 7px; padding: 7px 13px; border-radius: 99px; background: #eef2ff; color: #4F46E5; font-size: 11.5px; font-weight: 600; white-space: nowrap; }
    .cd-pill svg { width: 14px; height: 14px; }
    .cd-pill.off { background: #f1f5f9; color: #64748b; }

    /* AREA PESAN */
    .cd-messages { flex: 1; min-height: 0; overflow-y: auto; padding: 26px 34px; background: radial-gradient(circle at 50% 0%, rgba(124,58,237,.05), transparent 50%), #fcfbff; scrollbar-width: thin; scrollbar-color: #d7d0f2 transparent; }
    .cd-day { display: flex; align-items: center; gap: 12px; margin: 4px 0 20px; color: #a39cb4; font-size: 11px; font-weight: 600; }
    .cd-day::before, .cd-day::after { content: ""; flex: 1; height: 1px; background: #ece8f6; }
    .cd-message { display: flex; width: 100%; margin-bottom: 20px; }
    .cd-message.mine { justify-content: flex-end; }
    .cd-inner { display: flex; align-items: flex-end; gap: 9px; max-width: min(72%, 680px); }
    .cd-message.mine .cd-inner { flex-direction: row-reverse; }
    .cd-sm { width: 32px; height: 32px; flex-shrink: 0; display: grid; place-items: center; border-radius: 10px; color: #6d28d9; background: #ede9fe; font-size: 12px; font-weight: 700; }
    .cd-content { min-width: 0; }
    .cd-sender { margin: 0 0 6px 4px; color: #8b8495; font-size: 10.5px; font-weight: 600; }
    .cd-message.mine .cd-sender { margin: 0 4px 6px 0; text-align: right; }
    .cd-bubble { padding: 12px 16px; border: 1px solid #e9e5f5; border-radius: 18px; border-bottom-left-radius: 5px; color: #352d40; background: #fff; box-shadow: 0 4px 14px rgba(30,20,80,.05); font-size: 13.5px; line-height: 1.6; white-space: pre-wrap; overflow-wrap: anywhere; }
    .cd-message.mine .cd-bubble { border: 0; border-radius: 18px; border-bottom-right-radius: 5px; color: #fff; background: linear-gradient(135deg, #1E2A6B, #4F46E5); box-shadow: 0 10px 22px rgba(79,70,229,.25); }
    .cd-time { display: block; margin-top: 5px; color: #aaa3b1; font-size: 9.5px; line-height: 1; }
    .cd-message.mine .cd-time { text-align: right; }

    /* KONDISI KOSONG */
    .cd-empty { min-height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 10px 0; }
    .cd-mascot { width: 130px; height: 130px; object-fit: contain; margin-bottom: 6px; filter: drop-shadow(0 14px 22px rgba(79,70,229,.3)); animation: cd-bob 4s ease-in-out infinite; }
    @keyframes cd-bob { 0%, 100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-9px) rotate(2deg); } }
    .cd-empty h2 { margin: 0; color: #1E2A6B; font-size: 19px; font-weight: 700; letter-spacing: -.01em; }
    .cd-empty p { max-width: 430px; margin: 8px 0 0; color: #7b7490; font-size: 13px; line-height: 1.7; }
    .cd-steps { display: flex; gap: 12px; margin-top: 22px; flex-wrap: wrap; justify-content: center; }
    .cd-step { width: 190px; padding: 14px; border: 1px solid #e8e4ff; border-radius: 16px; background: #fff; text-align: left; box-shadow: 0 6px 16px rgba(79,70,229,.07); }
    .cd-step b { width: 24px; height: 24px; display: grid; place-items: center; margin-bottom: 8px; border-radius: 50%; color: #fff; font-size: 11px; background: linear-gradient(135deg, #4F46E5, #7C3AED); }
    .cd-step span { color: #475569; font-size: 12px; line-height: 1.5; }
    .cd-cta { margin-top: 22px; padding: 11px 22px; border-radius: 13px; color: #fff; text-decoration: none; font-size: 13px; font-weight: 600; background: linear-gradient(135deg, #1E2A6B, #4F46E5); box-shadow: 0 10px 22px rgba(79,70,229,.3); transition: .2s ease; }
    .cd-cta:hover { transform: translateY(-2px); box-shadow: 0 14px 28px rgba(79,70,229,.4); }
    .cd-sug { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; margin-top: 20px; max-width: 640px; }
    .cd-sug button { padding: 9px 14px; border: 1px solid #ddd8fb; border-radius: 99px; background: #fff; color: #4F46E5; font: inherit; font-size: 12px; font-weight: 500; cursor: pointer; transition: .2s ease; }
    .cd-sug button:hover { background: #4F46E5; border-color: #4F46E5; color: #fff; transform: translateY(-2px); }

    /* INPUT */
    .cd-input-area { flex-shrink: 0; padding: 14px 24px 16px; border-top: 1px solid #eeeaf5; background: #fff; }
    .cd-form { width: min(100%, 850px); margin: 0 auto; }
    .cd-box { display: flex; align-items: flex-end; gap: 10px; padding: 7px 8px 7px 18px; border: 1.5px solid #e4dff4; border-radius: 20px; background: #faf9ff; transition: .2s ease; }
    .cd-box:focus-within { border-color: #4F46E5; background: #fff; box-shadow: 0 0 0 4px rgba(79,70,229,.12); }
    .cd-box.locked { background: #f3f2f8; border-style: dashed; }
    .cd-input { flex: 1; min-width: 0; height: 42px; min-height: 42px; max-height: 130px; padding: 10px 0; border: 0; outline: 0; resize: none; color: #332b3d; background: transparent; font-family: inherit; font-size: 14px; line-height: 1.5; overflow: hidden; }
    .cd-input::placeholder { color: #aaa3b3; }
    .cd-input:disabled { cursor: not-allowed; }
    .cd-send { width: 42px; height: 42px; flex-shrink: 0; display: grid; place-items: center; border: 0; border-radius: 14px; color: #fff; background: linear-gradient(135deg, #1E2A6B, #4F46E5); cursor: pointer; box-shadow: 0 8px 18px rgba(79,70,229,.3); transition: .2s ease; }
    .cd-send:hover:not(:disabled) { transform: translateY(-1px) scale(1.04); box-shadow: 0 12px 24px rgba(79,70,229,.4); }
    .cd-send:disabled { opacity: .38; cursor: not-allowed; box-shadow: none; }
    .cd-send svg { width: 18px; height: 18px; }
    .cd-helper { margin: 8px 3px 0; color: #aaa3b1; font-size: 10.5px; text-align: center; }

    @media (prefers-reduced-motion: reduce) { .cd-page *, .cd-page *::before, .cd-page *::after { animation: none !important; transition: none !important; } }
    @media (max-width: 768px) {
        .cd-page { padding: 10px; }
        .cd-wrap { height: calc(100dvh - 84px); min-height: 0; border-radius: 22px; }
        .cd-header { padding: 12px 14px; }
        .cd-pill { display: none; }
        .cd-messages { padding: 20px 14px; }
        .cd-inner { max-width: 86%; }
        .cd-input-area { padding: 10px 12px 12px; }
        .cd-helper { display: none; }
        .cd-step { width: 100%; }
    }
    @media (max-width: 430px) {
        .cd-page { padding: 0; }
        .cd-wrap { width: 100%; height: calc(100dvh - 64px); border: 0; border-radius: 0; }
        .cd-sm { display: none; }
        .cd-mascot { width: 100px; height: 100px; }
    }
</style>


<div class="cd-page">

    <div class="cd-bg" aria-hidden="true">
        <div class="cd-grid"></div><i class="cd-orb one"></i><i class="cd-orb two"></i>
        <i class="cd-arw" style="--x:7%;  --s:22px; --d:19s; --dl:-3s"></i>
        <i class="cd-arw" style="--x:70%; --s:26px; --d:21s; --dl:-9s"></i>
        <i class="cd-arw" style="--x:93%; --s:18px; --d:24s; --dl:-15s"></i>
    </div>

    <div class="cd-wrap">

        {{-- HEADER --}}
        <header class="cd-header">
            <div class="cd-left">
                <a href="{{ url()->previous() }}" class="cd-back" aria-label="Kembali">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"></path><path d="M12 19l-7-7 7-7"></path></svg>
                </a>
                <div class="cd-avatar {{ $adaDosen ? '' : 'off' }}">{{ $inisial }}</div>
                <div class="cd-user">
                    <p class="cd-name">{{ $namaDosen }}</p>
                    <div class="cd-meta">
                        @if ($adaDosen)
                            Dosen PA @if ($nipDosen) · NIP {{ $nipDosen }} @endif
                        @else
                            Belum ada Dosen PA
                        @endif
                    </div>
                </div>
            </div>
            <span class="cd-pill {{ $adaDosen ? '' : 'off' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.5 7.2L4 20l1-4.3A8 8 0 1 1 21 12z"/></svg>
                {{ $adaDosen ? 'Konsultasi akademik' : 'Belum terhubung' }}
            </span>
        </header>


        {{-- AREA PESAN --}}
        <main class="cd-messages" id="cdMessages">

            @forelse ($messages as $message)

                @php
                    $mine = $message->sender_user_id === auth()->id();
                    $day  = $message->created_at->toDateString();
                @endphp

                @if ($day !== $lastDay)
                    @php $lastDay = $day; @endphp
                    <div class="cd-day">{{ $message->created_at->isToday() ? 'Hari ini' : ($message->created_at->isYesterday() ? 'Kemarin' : $message->created_at->locale('id')->translatedFormat('d F Y')) }}</div>
                @endif

                <div class="cd-message {{ $mine ? 'mine' : 'theirs' }}">
                    <div class="cd-inner">
                        @if (! $mine)<div class="cd-sm">{{ $inisial }}</div>@endif
                        <div class="cd-content">
                            <div class="cd-sender">{{ $mine ? 'Anda' : $namaDosen }}</div>
                            <div class="cd-bubble">{{ $message->message }}</div>
                            <span class="cd-time">{{ $message->created_at->format('H:i') }}</span>
                        </div>
                    </div>
                </div>

            @empty

                <div class="cd-empty">
                    <img class="cd-mascot" src="{{ asset('images/happy.png') }}" alt="NARA" onerror="this.style.display='none'">

                    @if ($adaDosen)
                        <h2>Mulai percakapan dengan {{ $namaDosen }}</h2>
                        <p>Tanyakan rekomendasi mata kuliah, jadwal bimbingan, atau rencana studimu. Pilih salah satu saran di bawah atau tulis sendiri.</p>
                        <div class="cd-sug">
                            @foreach ($saran as $s)
                                <button type="button" data-fill="{{ $s }}">{{ $s }}</button>
                            @endforeach
                        </div>
                    @else
                        <h2>Kamu belum punya Dosen PA</h2>
                        <p>Dosen PA ditetapkan oleh admin kampus. Sementara menunggu, kamu bisa bertanya ke NARA.</p>
                        <div class="cd-steps">
                            <div class="cd-step"><b>1</b><span>Admin kampus menetapkan Dosen PA untukmu</span></div>
                            <div class="cd-step"><b>2</b><span>Dosen PA muncul di halaman ini</span></div>
                            <div class="cd-step"><b>3</b><span>Mulai berkonsultasi langsung</span></div>
                        </div>
                        <a href="{{ route('chat') }}" class="cd-cta">Tanya NARA sekarang</a>
                    @endif
                </div>

            @endforelse

        </main>


        {{-- INPUT --}}
        <div class="cd-input-area">
            <form id="cdForm" class="cd-form" method="POST" action="{{ route('chat-dosen.send') }}">
                @csrf

                <div class="cd-box {{ $adaDosen ? '' : 'locked' }}">
                    <textarea
                        name="message"
                        id="cdInput"
                        class="cd-input"
                        maxlength="2000"
                        required
                        rows="1"
                        {{ $adaDosen ? '' : 'disabled' }}
                        placeholder="{{ $adaDosen ? 'Tulis pesan ke Dosen PA...' : 'Chat aktif setelah kamu memiliki Dosen PA' }}"
                        aria-label="Pesan ke Dosen PA"
                    ></textarea>

                    <button type="submit" id="cdSend" class="cd-send" disabled aria-label="Kirim pesan">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"></path><path d="M22 2l-7 20-4-9-9-4 20-7Z"></path></svg>
                    </button>
                </div>

                <p class="cd-helper">
                    {{ $adaDosen ? 'Enter untuk mengirim · Shift + Enter untuk baris baru' : 'Chat dibuka setelah admin menetapkan Dosen PA untukmu.' }}
                </p>
            </form>
        </div>

    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('cdForm');
    const input = document.getElementById('cdInput');
    const sendButton = document.getElementById('cdSend');
    const messages = document.getElementById('cdMessages');

    if (!form || !input || !sendButton) return;

    function updateSendButton() { sendButton.disabled = input.disabled || !input.value.trim(); }

    function resizeInput() {
        const maxHeight = 130;
        input.style.height = '42px';
        if (!input.value) { input.style.overflowY = 'hidden'; return; }
        input.style.height = Math.min(Math.max(input.scrollHeight, 42), maxHeight) + 'px';
        input.style.overflowY = input.scrollHeight > maxHeight ? 'auto' : 'hidden';
    }

    function scrollToBottom() { if (messages) messages.scrollTop = messages.scrollHeight; }

    input.addEventListener('input', function () { updateSendButton(); resizeInput(); });

    // Enter untuk kirim, Shift+Enter untuk baris baru
    input.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && !event.shiftKey && !event.isComposing) {
            event.preventDefault();
            if (!input.disabled && input.value.trim()) form.requestSubmit();
        }
    });

    form.addEventListener('submit', function (event) {
        if (input.disabled || !input.value.trim()) { event.preventDefault(); return; }
        sendButton.disabled = true;
    });

    // Saran pesan: klik untuk mengisi kolom input
    document.querySelectorAll('[data-fill]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (input.disabled) return;
            input.value = btn.dataset.fill;
            updateSendButton(); resizeInput(); input.focus();
        });
    });

    updateSendButton();
    resizeInput();
    scrollToBottom();
});
</script>

@endsection