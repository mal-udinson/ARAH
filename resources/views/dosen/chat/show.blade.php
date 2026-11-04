@extends('layouts.app')

@section('title', 'Chat — ARAH')

@section('content')

<style>
    /* =========================================================
       ARAH — DOSEN CHAT
       UI only — routes & backend structure tetap
    ========================================================= */

    .chat-page {
        position: relative;
        min-height: calc(100dvh - 64px);
        padding: 24px;
        overflow: hidden;
        background:
            radial-gradient(circle at 15% 15%, rgba(124, 58, 237, .22), transparent 28%),
            radial-gradient(circle at 85% 80%, rgba(168, 85, 247, .16), transparent 30%),
            linear-gradient(135deg, #f5f3ff 0%, #ede9fe 45%, #f8f7ff 100%);
    }

    /* Background decoration */
    .chat-bg {
        position: absolute;
        inset: 0;
        pointer-events: none;
        overflow: hidden;
    }

    .chat-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(2px);
        opacity: .5;
    }

    .chat-orb-1 {
        width: 280px;
        height: 280px;
        top: -120px;
        left: -90px;
        background: rgba(124, 58, 237, .14);
    }

    .chat-orb-2 {
        width: 360px;
        height: 360px;
        right: -150px;
        bottom: -170px;
        background: rgba(168, 85, 247, .12);
    }

    /* Main container */
    .chat-shell {
        position: relative;
        z-index: 2;
        width: min(100%, 1080px);
        height: calc(100dvh - 112px);
        min-height: 560px;
        margin: 0 auto;

        display: flex;
        flex-direction: column;

        overflow: hidden;
        border: 1px solid rgba(124, 58, 237, .10);
        border-radius: 24px;

        background: rgba(255, 255, 255, .92);
        box-shadow:
            0 24px 70px rgba(76, 29, 149, .13),
            0 4px 18px rgba(15, 23, 42, .05);

        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
    }

    /* =========================================================
       HEADER
    ========================================================= */

    .chat-header {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;

        min-height: 76px;
        padding: 14px 20px;

        border-bottom: 1px solid #eeeaf8;
        background: rgba(255, 255, 255, .94);
    }

    .chat-header-left {
        display: flex;
        align-items: center;
        gap: 13px;
        min-width: 0;
    }

    .chat-back {
        width: 40px;
        height: 40px;
        flex-shrink: 0;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        border: 1px solid #e9e4f5;
        border-radius: 12px;

        color: #625b71;
        background: #faf9fd;

        text-decoration: none;
        transition: .2s ease;
    }

    .chat-back:hover {
        color: #6d28d9;
        background: #f3efff;
        border-color: #ddd3fa;
        transform: translateX(-2px);
    }

    .chat-back svg {
        width: 19px;
        height: 19px;
    }

    .chat-avatar {
        width: 43px;
        height: 43px;
        flex-shrink: 0;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 14px;

        color: #fff;
        background: linear-gradient(135deg, #7c3aed, #9333ea);

        font-size: 17px;
        font-weight: 700;

        box-shadow: 0 7px 18px rgba(124, 58, 237, .22);
    }

    .chat-user-info {
        min-width: 0;
    }

    .chat-user-name {
        margin: 0;

        overflow: hidden;
        color: #211b2d;

        font-size: 15px;
        font-weight: 700;
        line-height: 1.35;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .chat-user-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 3px;

        color: #8a8297;
        font-size: 12px;
    }

    .chat-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, .10);
    }

    /* =========================================================
       MESSAGES
    ========================================================= */

    .chat-messages {
        flex: 1;
        min-height: 0;

        overflow-y: auto;
        padding: 28px 34px;

        background:
            radial-gradient(
                circle at 50% 0%,
                rgba(124, 58, 237, .035),
                transparent 45%
            );
    }

    .chat-messages::-webkit-scrollbar {
        width: 6px;
    }

    .chat-messages::-webkit-scrollbar-track {
        background: transparent;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        border-radius: 10px;
        background: #ddd7e9;
    }

    .chat-messages::-webkit-scrollbar-thumb:hover {
        background: #c8bfda;
    }

    .chat-message {
        display: flex;
        width: 100%;
        margin-bottom: 22px;
    }

    .chat-message:last-child {
        margin-bottom: 0;
    }

    .chat-message.theirs {
        justify-content: flex-start;
    }

    .chat-message.mine {
        justify-content: flex-end;
    }

    .chat-message-inner {
        display: flex;
        align-items: flex-end;
        gap: 9px;

        max-width: min(72%, 680px);
    }

    .chat-message.mine .chat-message-inner {
        flex-direction: row-reverse;
    }

    .chat-message-avatar {
        width: 32px;
        height: 32px;
        flex-shrink: 0;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 10px;

        color: #6d28d9;
        background: #f0eafa;

        font-size: 12px;
        font-weight: 700;
    }

    .chat-message-content {
        min-width: 0;
    }

    .chat-message-name {
        margin: 0 0 6px 4px;

        color: #8b8495;
        font-size: 11px;
        font-weight: 600;
    }

    .chat-message.mine .chat-message-name {
        margin-left: 0;
        margin-right: 4px;
        text-align: right;
    }

    .chat-bubble {
        position: relative;

        padding: 12px 15px;

        border-radius: 17px;

        color: #352d40;
        background: #fff;

        border: 1px solid #ece8f2;
        box-shadow: 0 3px 12px rgba(30, 20, 50, .045);

        font-size: 14px;
        line-height: 1.6;

        word-break: break-word;
        white-space: pre-wrap;
    }

    .chat-message.theirs .chat-bubble {
        border-bottom-left-radius: 5px;
    }

    .chat-message.mine .chat-bubble {
        border: 0;
        border-bottom-right-radius: 5px;

        color: #fff;
        background: linear-gradient(135deg, #7c3aed, #8b5cf6);

        box-shadow: 0 7px 18px rgba(124, 58, 237, .18);
    }

    .chat-time {
        display: block;
        margin-top: 5px;

        color: #aaa3b1;
        font-size: 10px;
        line-height: 1;
    }

    .chat-message.mine .chat-time {
        text-align: right;
    }

    /* Empty state */
    .chat-empty {
        height: 100%;

        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;

        text-align: center;
        color: #9991a5;
    }

    .chat-empty-icon {
        width: 58px;
        height: 58px;
        margin-bottom: 14px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 18px;

        color: #7c3aed;
        background: #f1edfb;
    }

    .chat-empty-icon svg {
        width: 27px;
        height: 27px;
    }

    .chat-empty p {
        margin: 0;

        font-size: 13px;
        line-height: 1.7;
    }

    /* =========================================================
       INPUT
    ========================================================= */

    .chat-input-area {
        flex-shrink: 0;

        padding: 14px 24px 17px;

        border-top: 1px solid #eeeaf5;
        background: rgba(255, 255, 255, .96);
    }

    .chat-form {
        width: min(100%, 850px);
        margin: 0 auto;
    }

    .chat-input-box {
        display: flex;
        align-items: flex-end;
        gap: 10px;

        padding: 7px 8px 7px 15px;

        border: 1px solid #e4dff0;
        border-radius: 18px;

        background: #fbfaff;

        transition: .2s ease;
    }

    .chat-input-box:focus-within {
        border-color: #b9a4ee;
        background: #fff;

        box-shadow:
            0 0 0 4px rgba(124, 58, 237, .06),
            0 5px 18px rgba(76, 29, 149, .06);
    }

    .chat-textarea {
        flex: 1;
        min-width: 0;

        max-height: 130px;
        min-height: 40px;

        padding: 9px 0;

        border: 0;
        outline: 0;
        resize: none;

        color: #332b3d;
        background: transparent;

        font-family: inherit;
        font-size: 14px;
        line-height: 1.5;
    }

    .chat-textarea::placeholder {
        color: #aaa3b3;
    }

    .chat-send {
        width: 40px;
        height: 40px;
        flex-shrink: 0;

        display: flex;
        align-items: center;
        justify-content: center;

        border: 0;
        border-radius: 13px;

        color: #fff;
        background: linear-gradient(135deg, #7c3aed, #8b5cf6);

        cursor: pointer;

        box-shadow: 0 6px 15px rgba(124, 58, 237, .20);

        transition: .2s ease;
    }

    .chat-send:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(124, 58, 237, .28);
    }

    .chat-send:active:not(:disabled) {
        transform: translateY(0);
    }

    .chat-send:disabled {
        opacity: .38;
        cursor: not-allowed;
        box-shadow: none;
    }

    .chat-send svg {
        width: 18px;
        height: 18px;
    }

    .chat-helper {
        margin: 7px 3px 0;

        color: #aaa3b1;
        font-size: 10px;
        text-align: center;
    }

    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 768px) {
        .chat-page {
            padding: 10px;
        }

        .chat-shell {
            height: calc(100dvh - 84px);
            min-height: 0;
            border-radius: 20px;
        }

        .chat-header {
            min-height: 68px;
            padding: 11px 13px;
        }

        .chat-back {
            width: 37px;
            height: 37px;
        }

        .chat-avatar {
            width: 39px;
            height: 39px;
            border-radius: 12px;
            font-size: 15px;
        }

        .chat-user-name {
            font-size: 14px;
        }

        .chat-user-meta {
            font-size: 11px;
        }

        .chat-messages {
            padding: 20px 14px;
        }

        .chat-message-inner {
            max-width: 84%;
        }

        .chat-message-avatar {
            width: 29px;
            height: 29px;
        }

        .chat-bubble {
            padding: 10px 13px;
            font-size: 13px;
        }

        .chat-input-area {
            padding: 10px 12px 13px;
        }

        .chat-input-box {
            border-radius: 16px;
            padding-left: 13px;
        }

        .chat-helper {
            display: none;
        }
    }

    @media (max-width: 430px) {
        .chat-page {
            padding: 0;
        }

        .chat-shell {
            width: 100%;
            height: 100dvh;
            border: 0;
            border-radius: 0;
        }

        .chat-message-inner {
            max-width: 88%;
        }

        .chat-message-avatar {
            display: none;
        }

        .chat-messages {
            padding: 18px 12px;
        }
    }
</style>


<div class="chat-page">

    {{-- Background decoration --}}
    <div class="chat-bg">
        <div class="chat-orb chat-orb-1"></div>
        <div class="chat-orb chat-orb-2"></div>
    </div>


    <div class="chat-shell">

        {{-- =====================================================
             HEADER
        ====================================================== --}}
        <header class="chat-header">

            <div class="chat-header-left">

                <a
                    href="{{ route('dosen.chat.index') }}"
                    class="chat-back"
                    aria-label="Kembali ke daftar chat"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M19 12H5"></path>
                        <path d="M12 19l-7-7 7-7"></path>
                    </svg>
                </a>

                <div class="chat-avatar">
                    {{ strtoupper(substr(trim($mahasiswa->nama), 0, 1)) }}
                </div>

                <div class="chat-user-info">
                    <p class="chat-user-name">
                        {{ $mahasiswa->nama }}
                    </p>

                    <div class="chat-user-meta">
                        <span class="chat-status-dot"></span>
                        <span>
                            Mahasiswa · NIM {{ $mahasiswa->nim }}
                        </span>
                    </div>
                </div>

            </div>

        </header>


        {{-- =====================================================
             MESSAGE AREA
        ====================================================== --}}
        <main
            class="chat-messages"
            id="chatMessages"
        >

            @forelse ($messages as $message)

                @php
                    $mine = $message->sender_user_id === auth()->id();
                @endphp

                <div class="chat-message {{ $mine ? 'mine' : 'theirs' }}">

                    <div class="chat-message-inner">

                        {{-- Avatar mahasiswa hanya untuk pesan masuk --}}
                        @if (!$mine)
                            <div class="chat-message-avatar">
                                {{ strtoupper(substr(trim($mahasiswa->nama), 0, 1)) }}
                            </div>
                        @endif

                        <div class="chat-message-content">

                            <div class="chat-message-name">
                                {{ $mine ? 'Anda' : $mahasiswa->nama }}
                            </div>

                            <div class="chat-bubble">
                                {{ $message->message }}
                            </div>

                            <span class="chat-time">
                                {{ $message->created_at->format('H:i') }}
                            </span>

                        </div>

                    </div>

                </div>

            @empty

                <div class="chat-empty">

                    <div class="chat-empty-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M21 11.5a8.38 8.38 0 0 1-1 4.1 8.5 8.5 0 0 1-7.5 4.4 8.38 8.38 0 0 1-4.1-1L3 21l1.9-5.4A8.38 8.38 0 0 1 4 11.5a8.5 8.5 0 0 1 4.4-7.5A8.38 8.38 0 0 1 12.5 3h.5a8.5 8.5 0 0 1 8 8v.5Z"></path>
                        </svg>
                    </div>

                    <p>
                        Belum ada percakapan dengan mahasiswa ini.<br>
                        Mulai percakapan untuk terhubung dengan mahasiswa.
                    </p>

                </div>

            @endforelse

        </main>


        {{-- =====================================================
             INPUT AREA
        ====================================================== --}}
        <div class="chat-input-area">

            <form
                id="chatForm"
                class="chat-form"
                method="POST"
                action="{{ route('dosen.chat.send', $mahasiswa->id) }}"
            >
                @csrf

                <div class="chat-input-box">

                    <textarea
                        name="message"
                        id="messageInput"
                        class="chat-textarea"
                        maxlength="2000"
                        required
                        rows="1"
                        placeholder="Tulis balasan..."
                        aria-label="Pesan untuk mahasiswa"
                    ></textarea>

                    <button
                        type="submit"
                        id="sendBtn"
                        class="chat-send"
                        disabled
                        aria-label="Kirim pesan"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M22 2L11 13"></path>
                            <path d="M22 2l-7 20-4-9-9-4 20-7Z"></path>
                        </svg>
                    </button>

                </div>

                <p class="chat-helper">
                    Enter untuk mengirim · Shift + Enter untuk baris baru
                </p>

            </form>

        </div>

    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        const form = document.getElementById('chatForm');
        const input = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');
        const messages = document.getElementById('chatMessages');

        if (!form || !input || !sendBtn) {
            return;
        }


        /* =====================================================
           UPDATE SEND BUTTON
        ====================================================== */

        function updateSendButton() {
            sendBtn.disabled = !input.value.trim();
        }


        /* =====================================================
           AUTO RESIZE TEXTAREA
        ====================================================== */

        function resizeTextarea() {
            input.style.height = 'auto';

            const maxHeight = 130;
            const newHeight = Math.min(input.scrollHeight, maxHeight);

            input.style.height = newHeight + 'px';
            input.style.overflowY =
                input.scrollHeight > maxHeight ? 'auto' : 'hidden';
        }


        /* =====================================================
           INPUT EVENT
        ====================================================== */

        input.addEventListener('input', function () {
            updateSendButton();
            resizeTextarea();
        });


        /* =====================================================
           KEYBOARD
        ====================================================== */

        input.addEventListener('keydown', function (event) {

            /*
             * Enter = kirim
             * Shift + Enter = baris baru
             */

            if (
                event.key === 'Enter' &&
                !event.shiftKey &&
                !event.isComposing
            ) {
                event.preventDefault();

                if (input.value.trim()) {
                    form.requestSubmit();
                }
            }
        });


        /* =====================================================
           FORM VALIDATION
        ====================================================== */

        form.addEventListener('submit', function (event) {

            if (!input.value.trim()) {
                event.preventDefault();
                return;
            }

            sendBtn.disabled = true;
        });


        /* =====================================================
           SCROLL TO BOTTOM
        ====================================================== */

        function scrollToBottom() {
            if (messages) {
                messages.scrollTop = messages.scrollHeight;
            }
        }


        updateSendButton();
        resizeTextarea();
        scrollToBottom();

    });
</script>

@endsection