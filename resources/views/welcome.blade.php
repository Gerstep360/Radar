<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#000000">
    <title>Radar La Guardia</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
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
            color: #000;
        }

        .card {
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

        .card-header {
            background: #000;
            padding: 2.5rem 2rem 2.25rem;
            text-align: center;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

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
            animation: ringOut 3.5s ease-out infinite;
        }

        .ring:nth-child(2) {
            animation: ringOut 3.5s ease-out .8s infinite;
        }

        .ring:nth-child(3) {
            animation: ringOut 3.5s ease-out 1.6s infinite;
        }

        @keyframes ringOut {
            0% {
                opacity: .5;
                width: 60px;
                height: 60px;
            }

            100% {
                opacity: 0;
                width: 300px;
                height: 300px;
            }
        }

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

        .logo-icon {
            width: 56px;
            height: 56px;
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

        .logo-icon svg {
            width: 28px;
            height: 28px;
            color: #fff;
        }

        .card-header h1 {
            font-size: 1.375rem;
            font-weight: 800;
            letter-spacing: -.02em;
            position: relative;
            z-index: 1;
            animation: slideUp .5s cubic-bezier(.16, 1, .3, 1) .35s both;
        }

        .live-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: .75rem;
            font-weight: 600;
            color: rgba(255, 255, 255, .5);
            margin-top: .375rem;
            position: relative;
            z-index: 1;
            animation: slideUp .5s cubic-bezier(.16, 1, .3, 1) .45s both;
        }

        .live-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #fff;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
                transform: scale(1)
            }

            50% {
                opacity: .2;
                transform: scale(.6)
            }
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

        .card-body {
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

        .tagline {
            font-size: .875rem;
            color: #888;
            line-height: 1.6;
            text-align: center;
            margin-bottom: 1.75rem;
        }

        .features {
            display: flex;
            flex-direction: column;
            gap: .625rem;
            margin-bottom: 1.75rem;
        }

        .feature {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .75rem 1rem;
            border-radius: 14px;
            background: #fafafa;
            border: 1px solid #f0f0f0;
            transition: transform .2s;
        }

        .feature:active {
            transform: scale(.98);
        }

        .f-icon {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #000;
            color: #fff;
        }

        .f-icon svg {
            width: 18px;
            height: 18px;
        }

        .feature span {
            font-size: .8125rem;
            font-weight: 600;
            color: #333;
        }

        .actions {
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            padding: .9375rem 1.5rem;
            border-radius: 14px;
            font-weight: 700;
            font-size: .875rem;
            text-decoration: none;
            transition: transform .15s, box-shadow .25s;
            border: none;
            cursor: pointer;
            font-family: inherit;
            position: relative;
            overflow: hidden;
        }

        .btn:active {
            transform: scale(.97);
        }

        .btn svg {
            width: 18px;
            height: 18px;
        }

        .btn-primary {
            background: #000;
            color: #fff;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .15);
        }

        .btn-primary:hover {
            box-shadow: 0 6px 24px rgba(0, 0, 0, .2);
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .06), transparent);
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

        .btn-secondary {
            background: #fff;
            color: #333;
            border: 1.5px solid #e5e5e5;
        }

        .btn-secondary:hover {
            background: #fafafa;
            border-color: #ccc;
        }

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
    </style>
</head>

<body>
    <div class="card">
        <div class="card-header">
            <div class="ring"></div>
            <div class="ring"></div>
            <div class="ring"></div>
            <div class="center-dot"></div>

            <div class="logo-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                </svg>
            </div>
            <h1>Radar La Guardia</h1>
            <div class="live-badge"><span class="live-dot"></span> EN VIVO</div>
        </div>

        <div class="card-body">
            <p class="tagline">Tu comunidad reportando en tiempo real.<br>Mapa interactivo de reportes ciudadanos.</p>

            <div class="features">
                <div class="feature">
                    <div class="f-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0115 0z" />
                        </svg>
                    </div>
                    <span>Reportes geolocalizados en tu zona</span>
                </div>
                <div class="feature">
                    <div class="f-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                    <span>Comunidad segura y verificada</span>
                </div>
                <div class="feature">
                    <div class="f-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                    </div>
                    <span>Alertas y actualizaciones en vivo</span>
                </div>
            </div>

            <div class="actions">
                @auth
                    <a href="{{ url('/denuncias') }}" class="btn btn-primary">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                        </svg>
                        Ir al mapa
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                        Iniciar sesion
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-secondary">
                            <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                            </svg>
                            Crear cuenta
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
    <div class="party-footer">2026 LIBRES &mdash; RADAR</div>
</body>

</html>
