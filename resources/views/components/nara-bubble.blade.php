@props([
    'message' => '',
    'title' => 'NARA Insight',
    'compact' => false,
])

<div class="nara-bubble {{ $compact ? 'nara-bubble-compact' : '' }}">

    <div class="nara-bubble-mascot">
        <img
            src="{{ asset('images/senyum.png') }}"
            alt="NARA"
        >
    </div>

    <div class="nara-bubble-content">

        <div class="nara-bubble-label">
            <span class="nara-bubble-dot"></span>
            {{ $title }}
        </div>

        <div class="nara-bubble-message">
            {{ $message }}
        </div>

    </div>

</div>

<style>
    .nara-bubble {
        position: relative;

        display: flex;
        align-items: center;

        gap: 14px;

        width: 100%;

        padding: 16px 18px;

        background: linear-gradient(
            135deg,
            #f5fcff 0%,
            #ffffff 100%
        );

        border: 1px solid #dceef6;

        border-radius: 18px;

        box-shadow:
            0 10px 30px rgba(0, 126, 190, .06);
    }

    /* speech-bubble tail */
    .nara-bubble::after {
        content: "";

        position: absolute;

        left: 42px;
        bottom: -7px;

        width: 13px;
        height: 13px;

        background: #f8fdff;

        border-right: 1px solid #dceef6;
        border-bottom: 1px solid #dceef6;

        transform: rotate(45deg);
    }

    .nara-bubble-mascot {
        position: relative;

        flex: 0 0 54px;

        width: 54px;
        height: 54px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 14px;

        background: #eefaff;

        overflow: hidden;
    }

    .nara-bubble-mascot img {
        width: 46px !important;
        height: 46px !important;

        max-width: 46px !important;
        max-height: 46px !important;

        object-fit: contain !important;

        display: block;
    }

    .nara-bubble-content {
        min-width: 0;

        position: relative;
        z-index: 2;
    }

    .nara-bubble-label {
        display: flex;
        align-items: center;

        gap: 6px;

        margin-bottom: 5px;

        color: #078fd2;

        font-size: 9px;
        font-weight: 850;

        text-transform: uppercase;

        letter-spacing: .08em;
    }

    .nara-bubble-dot {
        width: 6px;
        height: 6px;

        border-radius: 50%;

        background: #00a9ee;

        box-shadow:
            0 0 0 4px rgba(0, 169, 238, .08);
    }

    .nara-bubble-message {
        color: #4e687a;

        font-size: 11px;

        line-height: 1.75;

        font-weight: 550;
    }

    .nara-bubble-compact {
        padding: 13px 15px;

        border-radius: 15px;
    }

    .nara-bubble-compact .nara-bubble-mascot {
        flex-basis: 44px;

        width: 44px;
        height: 44px;

        border-radius: 12px;
    }

    .nara-bubble-compact .nara-bubble-mascot img {
        width: 37px !important;
        height: 37px !important;

        max-width: 37px !important;
        max-height: 37px !important;
    }

    .nara-bubble-compact .nara-bubble-message {
        font-size: 10px;

        line-height: 1.65;
    }

    @media (max-width: 700px) {

        .nara-bubble {
            gap: 11px;

            padding: 14px;
        }

        .nara-bubble-mascot {
            flex-basis: 46px;

            width: 46px;
            height: 46px;
        }

        .nara-bubble-mascot img {
            width: 39px !important;
            height: 39px !important;
        }

        .nara-bubble-message {
            font-size: 10px;
        }
    }
</style>