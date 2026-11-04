@extends('layouts.app')

@section('title', 'Voice NARA — ARAH')

@section('content')

<style>
    /* ==========================================
     * KEYFRAMES
     * ========================================== */
    @keyframes blobFloat {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33%      { transform: translate(40px, -30px) scale(1.08); }
        66%      { transform: translate(-30px, 25px) scale(.94); }
    }

    @keyframes ringPulse {
        0%   { r: 50px; opacity: .8; }
        100% { r: 82px; opacity: 0; }
    }

    @keyframes iconBreath {
        0%, 100% { transform: scale(1); }
        50%      { transform: scale(1.06); }
    }

    @keyframes haloSpin {
        to { transform: rotate(360deg); }
    }

    @keyframes barWave {
        0%, 100% { transform: scaleY(.25); }
        50%      { transform: scaleY(1); }
    }

    @keyframes dotBlink {
        0%, 100% { opacity: 1; }
        50%      { opacity: .3; }
    }

    /* ==========================================
     * LAYOUT & BACKGROUND
     * ========================================== */
    .nara-page {
        position: relative;
        min-height: calc(100vh - 64px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 36px 16px;
        overflow: hidden;
        background: linear-gradient(165deg, #120f3d 0%, #2a1f8a 50%, #5a2bc9 100%);
        isolation: isolate;
    }

    .nara-page::before,
    .nara-page::after {
        content: "";
        position: absolute;
        width: 420px;
        height: 420px;
        border-radius: 50%;
        filter: blur(70px);
        opacity: .55;
        z-index: -1;
        animation: blobFloat 18s ease-in-out infinite;
        pointer-events: none;
    }

    .nara-page::before {
        top: -120px;
        left: -100px;
        background: #7c5cff;
    }

    .nara-page::after {
        right: -120px;
        bottom: -140px;
        background: #3dd6f5;
        opacity: .35;
        animation-delay: -9s;
    }

    /* ==========================================
     * KARTU
     * ========================================== */
    .nara-card {
        position: relative;
        width: min(540px, 100%);
        padding: 44px 36px 38px;
        border-radius: 32px;
        color: #fff;
        text-align: center;
        background: linear-gradient(180deg, rgba(255,255,255,.12), rgba(255,255,255,.04));
        border: 1px solid rgba(255,255,255,.16);
        backdrop-filter: blur(28px) saturate(150%);
        -webkit-backdrop-filter: blur(28px) saturate(150%);
        box-shadow:
            0 40px 80px -30px rgba(5,3,40,.7),
            inset 0 1px 0 rgba(255,255,255,.18);
    }

    .nara-card::before {
        content: "";
        position: absolute;
        inset: -1px;
        border-radius: inherit;
        padding: 1px;
        background: linear-gradient(135deg, rgba(255,255,255,.4), rgba(255,255,255,0) 40%, rgba(160,120,255,.5));
        -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none;
    }

    /* ==========================================
     * ORB
     * ========================================== */
    .nara-orb {
        position: relative;
        width: 170px;
        height: 170px;
        margin: 0 auto 26px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .nara-halo {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: conic-gradient(from 0deg, transparent 0 60%, rgba(180,150,255,.9) 75%, transparent 90%);
        -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 3px), #000 calc(100% - 2px));
        mask: radial-gradient(farthest-side, transparent calc(100% - 3px), #000 calc(100% - 2px));
        opacity: .5;
        animation: haloSpin 8s linear infinite;
    }

    .nara-rings {
        position: absolute;
        inset: 0;
        width: 170px;
        height: 170px;
        overflow: visible;
    }

    .nara-ring {
        fill: none;
        stroke: rgba(190,160,255,.75);
        stroke-width: 1.2;
        animation: ringPulse 2.8s cubic-bezier(.3, 0, .5, 1) infinite;
    }

    .nara-ring:nth-child(2) { animation-delay: .93s; }
    .nara-ring:nth-child(3) { animation-delay: 1.86s; }

    .nara-core {
        position: relative;
        z-index: 2;
        width: 112px;
        height: 112px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background:
            radial-gradient(circle at 30% 28%, rgba(255,255,255,.35), rgba(255,255,255,0) 55%),
            linear-gradient(145deg, rgba(124,92,255,.55), rgba(61,214,245,.25));
        border: 1px solid rgba(255,255,255,.28);
        box-shadow: 0 0 40px rgba(124,92,255,.55), inset 0 0 20px rgba(255,255,255,.08);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        transition: box-shadow .5s ease;
    }

    .nara-core img {
        width: 84px;
        height: 84px;
        object-fit: contain;
        animation: iconBreath 3s ease-in-out infinite;
    }

    /* Saat sesi aktif (tombol Akhiri aktif) */
    .nara-card:has(#stopVoice:not([disabled])) .nara-ring {
        animation-duration: 1.5s;
    }

    .nara-card:has(#stopVoice:not([disabled])) .nara-halo {
        opacity: 1;
        animation-duration: 3s;
    }

    .nara-card:has(#stopVoice:not([disabled])) .nara-core {
        box-shadow: 0 0 70px rgba(160,130,255,.85), inset 0 0 24px rgba(255,255,255,.12);
    }

    /* ==========================================
     * TEKS
     * ========================================== */
    .nara-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        margin-bottom: 14px;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,.2);
        background: rgba(255,255,255,.07);
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: rgba(255,255,255,.8);
    }

    .nara-eyebrow::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #7ef0c4;
        box-shadow: 0 0 10px #7ef0c4;
    }

    .nara-title {
        margin: 0 0 14px;
        font-size: 44px;
        font-weight: 800;
        letter-spacing: -1px;
        background: linear-gradient(135deg, #ffffff 0%, #d9cfff 60%, #9fe9ff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* ==========================================
     * STATUS
     * ========================================== */
    .nara-status {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 56px;
        padding: 14px 18px;
        margin-bottom: 22px;
        border-radius: 18px;
        background: rgba(10,6,50,.25);
        border: 1px solid rgba(255,255,255,.1);
    }

    .nara-status p {
        margin: 0;
        color: rgba(255,255,255,.92);
        font-size: 14.5px;
        line-height: 1.5;
        font-weight: 500;
    }

    /* ==========================================
     * WAVEFORM
     * ========================================== */
    .nara-wave {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        height: 36px;
        margin-bottom: 22px;
    }

    .nara-wave span {
        width: 4px;
        height: 36px;
        border-radius: 4px;
        background: linear-gradient(180deg, #d9cfff, #7c5cff);
        transform: scaleY(.2);
        transition: transform .3s ease;
    }

    .nara-wave span:nth-child(2) { animation-delay: .15s; }
    .nara-wave span:nth-child(3) { animation-delay: .3s; }
    .nara-wave span:nth-child(4) { animation-delay: .45s; }
    .nara-wave span:nth-child(5) { animation-delay: .6s; }

    .nara-card:has(#stopVoice:not([disabled])) .nara-wave span {
        animation: barWave 1.1s ease-in-out infinite;
    }

    /* ==========================================
     * TOMBOL
     * ========================================== */
    .nara-actions {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 24px;
    }

    .nara-btn {
        min-height: 54px;
        padding: 0 24px;
        border-radius: 18px;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: .2px;
        cursor: pointer;
        transition: transform .2s ease, box-shadow .25s ease, background .25s ease, opacity .25s ease;
    }

    .nara-btn:focus-visible {
        outline: 3px solid rgba(190,160,255,.8);
        outline-offset: 3px;
    }

    .nara-btn:disabled {
        cursor: not-allowed;
    }

    .nara-btn:not(:disabled):active {
        transform: scale(.97);
    }

    .nara-btn-primary {
        flex: 1 1 200px;
        border: 0;
        background: linear-gradient(135deg, #ffffff 0%, #e9e3ff 100%);
        color: #2a1a6b;
        box-shadow: 0 14px 30px -10px rgba(255,255,255,.45);
    }

    .nara-btn-primary:not(:disabled):hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 36px -10px rgba(255,255,255,.55), 0 0 28px rgba(124,92,255,.5);
    }

    .nara-btn-ghost {
        flex: 0 1 160px;
        border: 1.5px solid rgba(255,255,255,.28);
        background: rgba(255,255,255,.06);
        color: #fff;
    }

    .nara-btn-ghost:not(:disabled):hover {
        transform: translateY(-2px);
        background: rgba(255,255,255,.14);
        border-color: rgba(255,255,255,.45);
    }

    .nara-btn-ghost:disabled {
        opacity: .4;
    }

    /* ==========================================
     * INDIKATOR
     * ========================================== */
    .nara-indicator {
        display: inline-block;
        margin-bottom: 22px;
        padding: 6px 14px;
        border-radius: 999px;
        background: rgba(255,255,255,.06);
        color: rgba(255,255,255,.8);
        font-size: 13px;
        letter-spacing: .3px;
        font-weight: 500;
    }

    /* ==========================================
     * TRANSKRIP
     * ========================================== */
    .nara-transcript {
        display: none;
        margin-bottom: 18px;
        padding: 18px 20px;
        border-radius: 20px;
        background: rgba(10,6,50,.3);
        border: 1px solid rgba(255,255,255,.1);
        color: rgba(255,255,255,.92);
        text-align: left;
        font-size: 14px;
        line-height: 1.7;
        max-height: 280px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,.25) transparent;
    }

    .nara-transcript br {
        display: none;
    }

    .nara-transcript strong {
        display: block;
        margin: 14px 0 4px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #b9a8ff;
    }

    .nara-transcript strong:first-child {
        margin-top: 0;
    }

    /* ==========================================
     * ERROR
     * ========================================== */
    .nara-error {
        display: none;
        margin-bottom: 18px;
        padding: 14px 18px;
        border-radius: 16px;
        background: rgba(239,68,68,.14);
        border: 1px solid rgba(248,113,113,.35);
        color: #fecaca;
        text-align: left;
        font-size: 13px;
        line-height: 1.6;
        word-break: break-word;
    }

    /* ==========================================
     * RESPONSIVE & A11Y
     * ========================================== */
    @media (max-width: 480px) {
        .nara-card {
            padding: 34px 22px 30px;
            border-radius: 26px;
        }

        .nara-title {
            font-size: 36px;
        }

        .nara-btn-ghost {
            flex: 1 1 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .nara-page::before,
        .nara-page::after,
        .nara-halo,
        .nara-ring,
        .nara-core img,
        .nara-wave span {
            animation: none !important;
        }
    }
</style>

<div class="nara-page">

    <div class="nara-card">

        <!-- Orb -->
        <div class="nara-orb">

            <div class="nara-halo"></div>

            <svg class="nara-rings" viewBox="0 0 170 170" aria-hidden="true">
                <circle cx="85" cy="85" r="50" class="nara-ring"></circle>
                <circle cx="85" cy="85" r="50" class="nara-ring"></circle>
                <circle cx="85" cy="85" r="50" class="nara-ring"></circle>
            </svg>

            <div class="nara-core">
                <img
                    src="{{ asset('images/happy.png') }}"
                    alt="NARA"
                >
            </div>

        </div>

        <!-- Header -->
        <div class="nara-eyebrow">
            ARAH Voice Assistant
        </div>

        <h1 class="nara-title">
            NARA
        </h1>

        <!-- Status -->
        <div class="nara-status">
            <p id="voiceStatus">
                Tekan tombol untuk mulai ngobrol dengan NARA.
            </p>
        </div>

        <!-- Waveform -->
        <div class="nara-wave" aria-hidden="true">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>

        <!-- Buttons -->
        <div class="nara-actions">

            <button
                id="startVoice"
                type="button"
                class="nara-btn nara-btn-primary"
            >
                🎙️ Mulai Ngobrol
            </button>

            <button
                id="stopVoice"
                type="button"
                class="nara-btn nara-btn-ghost"
                disabled
            >
                Akhiri
            </button>

        </div>

        <!-- Indicator -->
        <div>
            <span id="voiceIndicator" class="nara-indicator">
                NARA siap.
            </span>
        </div>

        <!-- Transcript -->
        <div
            id="transcript"
            class="nara-transcript"
        ></div>

        <!-- Error -->
        <div
            id="errorBox"
            class="nara-error"
            role="alert"
        ></div>

    </div>

</div>


<script type="module">

import {
    GoogleGenAI,
    Modality
} from 'https://esm.run/@google/genai';


document.addEventListener(
    'DOMContentLoaded',
    function () {

        const startButton =
            document.getElementById(
                'startVoice'
            );

        const stopButton =
            document.getElementById(
                'stopVoice'
            );

        const status =
            document.getElementById(
                'voiceStatus'
            );

        const indicator =
            document.getElementById(
                'voiceIndicator'
            );

        const transcript =
            document.getElementById(
                'transcript'
            );

        const errorBox =
            document.getElementById(
                'errorBox'
            );


        let session = null;

        let inputContext = null;

        let inputStream = null;

        let inputSource = null;

        let inputProcessor = null;

        let silentGain = null;

        let outputContext = null;

        let nextAudioTime = 0;

        let running = false;


        function setStatus(message) {

            status.textContent =
                message;
        }


        function setIndicator(message) {

            indicator.textContent =
                message;
        }


        function showError(message) {

            errorBox.style.display =
                'block';

            errorBox.textContent =
                message;
        }


        function clearError() {

            errorBox.style.display =
                'none';

            errorBox.textContent =
                '';
        }


        function setButtons(active) {

            startButton.disabled =
                active;

            stopButton.disabled =
                !active;

            startButton.style.opacity =
                active ? '.5' : '1';

            stopButton.style.opacity =
                active ? '1' : '.4';
        }


        function floatToPcm16(float32Array) {

            const pcm =
                new Int16Array(
                    float32Array.length
                );


            for (
                let i = 0;
                i < float32Array.length;
                i++
            ) {

                const sample =
                    Math.max(
                        -1,
                        Math.min(
                            1,
                            float32Array[i]
                        )
                    );


                pcm[i] =
                    sample < 0
                        ? sample * 0x8000
                        : sample * 0x7fff;
            }


            return pcm;
        }


        function pcm16ToBase64(pcm) {

            const bytes =
                new Uint8Array(
                    pcm.buffer
                );

            let binary = '';

            const chunkSize =
                0x8000;


            for (
                let i = 0;
                i < bytes.length;
                i += chunkSize
            ) {

                binary += String.fromCharCode(
                    ...bytes.subarray(
                        i,
                        Math.min(
                            i + chunkSize,
                            bytes.length
                        )
                    )
                );
            }


            return btoa(binary);
        }


        function base64ToPcm16(base64) {

            const binary =
                atob(base64);

            const bytes =
                new Uint8Array(
                    binary.length
                );


            for (
                let i = 0;
                i < binary.length;
                i++
            ) {

                bytes[i] =
                    binary.charCodeAt(i);
            }


            return new Int16Array(
                bytes.buffer
            );
        }


        async function playAudio(base64) {

            if (!outputContext) {

                outputContext =
                    new (
                        window.AudioContext ||
                        window.webkitAudioContext
                    )({
                        sampleRate:24000
                    });


                nextAudioTime =
                    outputContext.currentTime;
            }


            if (
                outputContext.state ===
                'suspended'
            ) {

                await outputContext.resume();
            }


            const pcm =
                base64ToPcm16(
                    base64
                );


            const buffer =
                outputContext.createBuffer(
                    1,
                    pcm.length,
                    24000
                );


            const channel =
                buffer.getChannelData(0);


            for (
                let i = 0;
                i < pcm.length;
                i++
            ) {

                channel[i] =
                    pcm[i] / 32768;
            }


            const source =
                outputContext
                    .createBufferSource();


            source.buffer =
                buffer;


            source.connect(
                outputContext.destination
            );


            const startAt =
                Math.max(
                    outputContext.currentTime,
                    nextAudioTime
                );


            source.start(
                startAt
            );


            nextAudioTime =
                startAt +
                buffer.duration;
        }


        async function startMicrophone() {

            inputContext =
                new (
                    window.AudioContext ||
                    window.webkitAudioContext
                )({
                    sampleRate:16000
                });


            await inputContext.resume();


            inputStream =
                await navigator
                    .mediaDevices
                    .getUserMedia({
                        audio:{
                            channelCount:1,
                            echoCancellation:true,
                            noiseSuppression:true,
                            autoGainControl:true
                        }
                    });


            inputSource =
                inputContext
                    .createMediaStreamSource(
                        inputStream
                    );


            inputProcessor =
                inputContext
                    .createScriptProcessor(
                        4096,
                        1,
                        1
                    );


            silentGain =
                inputContext.createGain();


            silentGain.gain.value =
                0;


            inputSource.connect(
                inputProcessor
            );


            inputProcessor.connect(
                silentGain
            );


            silentGain.connect(
                inputContext.destination
            );


            inputProcessor.onaudioprocess =
                function (event) {

                    if (
                        !running ||
                        !session
                    ) {
                        return;
                    }


                    const input =
                        event
                            .inputBuffer
                            .getChannelData(0);


                    const pcm =
                        floatToPcm16(
                            input
                        );


                    const base64 =
                        pcm16ToBase64(
                            pcm
                        );


                    try {

                        session.sendRealtimeInput({
                            audio:{
                                data:base64,
                                mimeType:
                                    'audio/pcm;rate=16000'
                            }
                        });

                    } catch (error) {

                        console.error(
                            'Audio send error:',
                            error
                        );
                    }
                };
        }


        function stopMicrophone() {

            if (inputProcessor) {

                inputProcessor
                    .onaudioprocess = null;

                try {
                    inputProcessor.disconnect();
                } catch (e) {}
            }


            if (inputSource) {

                try {
                    inputSource.disconnect();
                } catch (e) {}
            }


            if (silentGain) {

                try {
                    silentGain.disconnect();
                } catch (e) {}
            }


            if (inputStream) {

                inputStream
                    .getTracks()
                    .forEach(
                        track =>
                            track.stop()
                    );
            }


            inputProcessor = null;
            inputSource = null;
            silentGain = null;
            inputStream = null;
        }


        async function cleanup() {

            running = false;


            stopMicrophone();


            if (session) {

                try {
                    session.close();
                } catch (e) {}
            }


            session = null;


            if (inputContext) {

                try {
                    await inputContext.close();
                } catch (e) {}
            }


            inputContext = null;


            if (outputContext) {

                try {
                    await outputContext.close();
                } catch (e) {}
            }


            outputContext = null;

            nextAudioTime = 0;


            setButtons(false);
        }


        async function startVoice() {

            if (
                session ||
                running
            ) {
                return;
            }


            clearError();


            transcript.style.display =
                'none';

            transcript.textContent =
                '';


            setButtons(true);


            setStatus(
                'Membuat sesi voice NARA...'
            );


            setIndicator(
                'Meminta token aman Gemini...'
            );


            try {

                /*
                 * ==================================
                 * 1. MINTA EPHEMERAL TOKEN
                 * ==================================
                 */

                const response =
                    await fetch(
                        "{{ route('nara.voice.session') }}",
                        {
                            method:'POST',

                            headers:{
                                'X-CSRF-TOKEN':
                                    document
                                        .querySelector(
                                            'meta[name="csrf-token"]'
                                        )
                                        .getAttribute(
                                            'content'
                                        ),

                                'Accept':
                                    'application/json'
                            }
                        }
                    );


                const data =
                    await response.json();


                if (!response.ok) {

                    let details = '';

                    if (
                        data.details
                    ) {

                        details =
                            typeof data.details ===
                            'string'
                                ? data.details
                                : JSON.stringify(
                                    data.details
                                );
                    }


                    throw new Error(
                        (data.error ||
                            'Gagal membuat token Gemini.')
                        +
                        (
                            details
                                ? ' — ' + details
                                : ''
                        )
                    );
                }


                const token =
                    data.token;

                const model =
                    data.model ||
                    'gemini-3.8-live';

                const instructions =
                    data.instructions ||
                    'Kamu adalah NARA, asisten akademik yang ramah.';


                if (!token) {

                    throw new Error(
                        'Token Gemini kosong.'
                    );
                }


                /*
                 * ==================================
                 * 2. AUDIO CONTEXT
                 * ==================================
                 */

                outputContext =
                    new (
                        window.AudioContext ||
                        window.webkitAudioContext
                    )({
                        sampleRate:24000
                    });


                await outputContext.resume();


                /*
                 * ==================================
                 * 3. GOOGLE GENAI SDK
                 * ==================================
                 *
                 * Tidak ada WebSocket manual.
                 */

                const ai =
                    new GoogleGenAI({
                        apiKey:token
                    });


                setStatus(
                    'Menghubungkan ke NARA...'
                );


                setIndicator(
                    'Membuka sesi Gemini Live...'
                );


                /*
                 * ==================================
                 * 4. LIVE CONNECT
                 * ==================================
                 */

                session =
                    await ai.live.connect({

                        model:model,

                        config:{

                            responseModalities:[
                                Modality.AUDIO
                            ],

                            systemInstruction:{
                                parts:[
                                    {
                                        text:
                                            instructions
                                    }
                                ]
                            },

                            inputAudioTranscription:{},

                            outputAudioTranscription:{},

                            speechConfig:{
                                voiceConfig:{
                                    prebuiltVoiceConfig:{
                                        voiceName:
                                            'Leda'
                                    }
                                }
                            }
                        },


                        callbacks:{

                            onopen:function () {

                                console.log(
                                    'Gemini Live OPEN ✅'
                                );

                                setStatus(
                                    'NARA siap. Silakan bicara.'
                                );

                                setIndicator(
                                    '🎙️ Mendengarkan...'
                                );
                            },


                            onmessage:
                                async function (
                                    message
                                ) {

                                    console.log(
                                        'NARA MESSAGE:',
                                        message
                                    );


                                    const serverContent =
                                        message.serverContent;


                                    if (
                                        !serverContent
                                    ) {
                                        return;
                                    }


                                    /*
                                     * INPUT TRANSCRIPTION
                                     */

                                    if (
                                        serverContent
                                            .inputTranscription
                                            ?.text
                                    ) {

                                        transcript
                                            .style
                                            .display =
                                            'block';


                                        transcript.innerHTML =
                                            '<strong>Kamu:</strong><br>' +
                                            serverContent
                                                .inputTranscription
                                                .text;
                                    }


                                    /*
                                     * OUTPUT TRANSCRIPTION
                                     */

                                    if (
                                        serverContent
                                            .outputTranscription
                                            ?.text
                                    ) {

                                        transcript
                                            .style
                                            .display =
                                            'block';


                                        transcript.innerHTML +=
                                            '<br><br>' +
                                            '<strong>NARA:</strong><br>' +
                                            serverContent
                                                .outputTranscription
                                                .text;
                                    }


                                    /*
                                     * AUDIO NARA
                                     */

                                    if (
                                        serverContent
                                            .modelTurn
                                            ?.parts
                                    ) {

                                        setStatus(
                                            'NARA sedang menjawab...'
                                        );


                                        setIndicator(
                                            '🔊 NARA berbicara...'
                                        );


                                        for (
                                            const part of
                                                serverContent
                                                    .modelTurn
                                                    .parts
                                        ) {

                                            const audioData =
                                                part
                                                    .inlineData
                                                    ?.data;


                                            if (
                                                audioData
                                            ) {

                                                await playAudio(
                                                    audioData
                                                );
                                            }
                                        }
                                    }


                                    /*
                                     * TURN SELESAI
                                     */

                                    if (
                                        serverContent
                                            .turnComplete
                                    ) {

                                        setStatus(
                                            'Silakan lanjut bicara.'
                                        );


                                        setIndicator(
                                            '🎙️ Mendengarkan...'
                                        );
                                    }
                                },


                            onerror:function (error) {

                                console.error(
                                    'Gemini Live Error:',
                                    error
                                );


                                showError(
                                    error?.message ||
                                    'Gemini Live mengalami error.'
                                );


                                setStatus(
                                    'Voice NARA gagal.'
                                );


                                setIndicator(
                                    'Terjadi error pada Gemini Live.'
                                );
                            },


                            onclose:function (event) {

                                console.warn(
                                    'Gemini Live Close:',
                                    event
                                );


                                if (running) {

                                    setStatus(
                                        'Percakapan selesai.'
                                    );


                                    setIndicator(
                                        'Koneksi NARA ditutup.'
                                    );
                                }


                                cleanup();
                            }
                        }
                    });


                /*
                 * ==================================
                 * 5. MICROPHONE
                 * ==================================
                 *
                 * connect() selesai -> sesi sudah
                 * berhasil dibuat.
                 */

                await startMicrophone();


                running = true;


                setStatus(
                    'NARA siap. Silakan bicara.'
                );


                setIndicator(
                    '🎙️ Mendengarkan...'
                );

            } catch (error) {

                console.error(
                    'NARA VOICE ERROR:',
                    error
                );


                showError(
                    error.message ||
                    'Voice NARA gagal dijalankan.'
                );


                setStatus(
                    'Voice NARA gagal.'
                );


                setIndicator(
                    'Periksa token, microphone, atau koneksi Gemini.'
                );


                await cleanup();
            }
        }


        async function stopVoice() {

            setStatus(
                'Mengakhiri percakapan...'
            );


            setIndicator(
                'Menutup koneksi NARA.'
            );


            await cleanup();


            setStatus(
                'Percakapan selesai.'
            );


            setIndicator(
                'NARA siap digunakan lagi.'
            );
        }


        setButtons(false);


        startButton.addEventListener(
            'click',
            startVoice
        );


        stopButton.addEventListener(
            'click',
            stopVoice
        );

    });

</script>

@endsection