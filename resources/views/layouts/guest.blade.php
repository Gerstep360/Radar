<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#000000">
    <title>{{ config('app.name', 'Radar') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Instrument Sans', -apple-system, system-ui, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            -webkit-font-smoothing: antialiased;
        }

        /* === Card === */
        .auth-card {
            background: #fff;
            border-radius: 28px;
            box-shadow: 0 10px 50px rgba(0, 0, 0, .1), 0 1px 3px rgba(0, 0, 0, .05);
            max-width: 400px;
            width: calc(100% - 2rem);
            margin: 1rem;
            overflow: hidden;
            animation: cardIn .7s cubic-bezier(.16, 1, .3, 1) both;
        }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(40px) scale(.96);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* === Header — PURE BLACK === */
        .auth-header {
            background: #000;
            padding: 2.25rem 2rem 2rem;
            text-align: center;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        /* Animated rings */
        .ring {
            position: absolute;
            top: 50%;
            left: 50%;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, .06);
            transform: translate(-50%, -50%);
            pointer-events: none;
        }

        .ring:nth-child(1) {
            width: 120px;
            height: 120px;
            animation: ringOut 3.5s ease-out infinite;
        }

        .ring:nth-child(2) {
            width: 120px;
            height: 120px;
            animation: ringOut 3.5s ease-out .8s infinite;
        }

        .ring:nth-child(3) {
            width: 120px;
            height: 120px;
            animation: ringOut 3.5s ease-out 1.6s infinite;
        }

        @keyframes ringOut {
            0% {
                opacity: .5;
                width: 60px;
                height: 60px;
                border-color: rgba(255, 255, 255, .1);
            }

            100% {
                opacity: 0;
                width: 300px;
                height: 300px;
                border-color: rgba(255, 255, 255, .02);
            }
        }

        /* Breathing center dot */
        .center-dot {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .2);
            transform: translate(-50%, -50%);
            animation: breathe 2s ease-in-out infinite;
        }

        @keyframes breathe {

            0%,
            100% {
                opacity: .2;
                transform: translate(-50%, -50%) scale(1)
            }

            50% {
                opacity: .5;
                transform: translate(-50%, -50%) scale(1.5)
            }
        }

        .auth-logo {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .12);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: .75rem;
            position: relative;
            z-index: 1;
            animation: logoIn .6s cubic-bezier(.16, 1, .3, 1) .25s both;
        }

        @keyframes logoIn {
            from {
                opacity: 0;
                transform: scale(.3) rotate(-10deg)
            }

            to {
                opacity: 1;
                transform: scale(1) rotate(0)
            }
        }

        .auth-logo svg {
            width: 24px;
            height: 24px;
            color: #fff;
        }

        .auth-header h1 {
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: -.02em;
            position: relative;
            z-index: 1;
            animation: slideUp .5s cubic-bezier(.16, 1, .3, 1) .35s both;
        }

        .auth-header p {
            font-size: .8rem;
            color: rgba(255, 255, 255, .45);
            font-weight: 500;
            margin-top: .25rem;
            position: relative;
            z-index: 1;
            animation: slideUp .5s cubic-bezier(.16, 1, .3, 1) .45s both;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(10px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        /* === Body === */
        .auth-body {
            padding: 1.75rem 1.75rem 2rem;
            animation: fadeUp .6s cubic-bezier(.16, 1, .3, 1) .4s both;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(16px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        /* === Form === */
        .form-group {
            margin-bottom: 1.125rem;
        }

        .form-label {
            display: block;
            font-size: .8125rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: .375rem;
        }

        .form-input {
            width: 100%;
            padding: .8125rem 1rem;
            border-radius: 14px;
            border: 1.5px solid #e5e5e5;
            background: #fff;
            font-size: .875rem;
            color: #000;
            transition: border-color .25s ease, box-shadow .25s ease;
            outline: none;
            font-family: inherit;
            -webkit-appearance: none;
        }

        .form-input::placeholder {
            color: #999;
        }

        .form-input:focus {
            border-color: #000;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, .05);
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: .5rem;
            cursor: pointer;
        }

        .form-check input[type="checkbox"] {
            width: 18px;
            height: 18px;
            border-radius: 5px;
            border: 1.5px solid #ccc;
            appearance: none;
            -webkit-appearance: none;
            background: #fff;
            cursor: pointer;
            transition: all .2s;
            display: grid;
            place-content: center;
        }

        .form-check input[type="checkbox"]:checked {
            background: #000;
            border-color: #000;
        }

        .form-check input[type="checkbox"]:checked::before {
            content: '';
            width: 10px;
            height: 10px;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 14 14' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M3 7l3 3 5-6' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-size: contain;
        }

        .form-check span {
            font-size: .8125rem;
            color: #666;
            font-weight: 500;
        }

        .form-link {
            font-size: .75rem;
            font-weight: 700;
            color: #000;
            text-decoration: none;
            opacity: .6;
            transition: opacity .2s;
        }

        .form-link:hover {
            opacity: 1;
        }

        /* === Button === */
        .btn-submit {
            width: 100%;
            padding: .9375rem;
            border-radius: 14px;
            background: #000;
            color: #fff;
            font-weight: 700;
            font-size: .875rem;
            border: none;
            cursor: pointer;
            font-family: inherit;
            position: relative;
            overflow: hidden;
            transition: transform .15s, box-shadow .25s;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .15);
        }

        .btn-submit:hover {
            box-shadow: 0 6px 24px rgba(0, 0, 0, .2);
        }

        .btn-submit:active {
            transform: scale(.97);
        }

        /* Shimmer effect on button */
        .btn-submit::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .08), transparent);
            animation: shimmer 3s ease infinite;
        }

        @keyframes shimmer {
            0% {
                left: -100%
            }

            50% {
                left: 100%
            }

            100% {
                left: 100%
            }
        }

        /* === Footer === */
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: .8125rem;
            color: #666;
        }

        .auth-footer a {
            color: #000;
            font-weight: 700;
            text-decoration: none;
            transition: opacity .2s;
        }

        .auth-footer a:hover {
            opacity: .7;
        }

        .error-text {
            color: #dc2626;
            font-size: .75rem;
            margin-top: .25rem;
            font-weight: 500;
        }

        /* Party branding footer */
        .party-footer {
            text-align: center;
            margin-top: 1.25rem;
            font-size: .6875rem;
            font-weight: 700;
            color: #999;
            letter-spacing: .1em;
            text-transform: uppercase;
            animation: slideUp .5s cubic-bezier(.16, 1, .3, 1) .6s both;
        }

        @media (hover: none) {
            .form-input {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    <div class="auth-card">
        <div class="auth-header">
            {{-- Animated expanding rings --}}
            <div class="ring"></div>
            <div class="ring"></div>
            <div class="ring"></div>
            <div class="center-dot"></div>

            <div class="auth-logo">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                </svg>
            </div>
            <h1>Radar La Guardia</h1>
            <p>{{ $headerDescription ?? '' }}</p>
        </div>
        <div class="auth-body">
            {{ $slot }}
        </div>
    </div>
    <div class="party-footer">2026 LIBRES &mdash; RADAR</div>
</body>

</html>
