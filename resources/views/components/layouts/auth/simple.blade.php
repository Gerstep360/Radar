<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
    <style>
        /* ===== RADAR AUTH — Premium Design System ===== */
        .auth-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 40%, #0ea5e9 100%);
            position: relative;
            overflow: hidden;
        }

        /* Radar pulse animation */
        .radar-pulse {
            position: absolute;
            border-radius: 50%;
            border: 2px solid rgba(56, 189, 248, 0.15);
            animation: radarPulse 4s ease-out infinite;
        }

        .radar-pulse:nth-child(2) {
            animation-delay: 1s;
        }

        .radar-pulse:nth-child(3) {
            animation-delay: 2s;
        }

        .radar-pulse:nth-child(4) {
            animation-delay: 3s;
        }

        @keyframes radarPulse {
            0% {
                width: 40px;
                height: 40px;
                opacity: 0.8;
            }

            100% {
                width: 600px;
                height: 600px;
                opacity: 0;
            }
        }

        /* Floating dots (simulating map markers) */
        .float-dot {
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            animation: floatDot 6s ease-in-out infinite;
        }

        @keyframes floatDot {

            0%,
            100% {
                transform: translateY(0) scale(1);
                opacity: 0.6;
            }

            50% {
                transform: translateY(-12px) scale(1.2);
                opacity: 1;
            }
        }

        /* Glassmorphism card for mobile */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        /* Subtle grid pattern */
        .grid-pattern {
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
        }
    </style>
</head>

<body class="min-h-screen antialiased">
    <div class="min-h-screen flex">

        {{-- LEFT PANEL — Animated Radar Background --}}
        <div class="hidden lg:flex lg:w-1/2 auth-bg grid-pattern items-center justify-center relative">
            {{-- Radar pulses from center --}}
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="radar-pulse"></div>
                <div class="radar-pulse"></div>
                <div class="radar-pulse"></div>
                <div class="radar-pulse"></div>
                {{-- Center dot --}}
                <div class="w-3 h-3 rounded-full bg-sky-400 shadow-lg shadow-sky-400/50 z-10"></div>
            </div>

            {{-- Floating map-like dots --}}
            <div class="float-dot bg-yellow-400" style="top: 25%; left: 20%; animation-delay: 0s;"></div>
            <div class="float-dot bg-red-500" style="top: 40%; left: 70%; animation-delay: 1.5s;"></div>
            <div class="float-dot bg-green-400" style="top: 65%; left: 35%; animation-delay: 3s;"></div>
            <div class="float-dot bg-blue-400" style="top: 30%; left: 55%; animation-delay: 0.8s;"></div>
            <div class="float-dot bg-yellow-400" style="top: 70%; left: 75%; animation-delay: 2.2s;"></div>
            <div class="float-dot bg-red-400" style="top: 80%; left: 15%; animation-delay: 4s;"></div>

            {{-- Branding --}}
            <div class="relative z-10 text-center px-12">
                <div class="inline-flex items-center gap-3 mb-6">
                    <div
                        class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center">
                        <svg class="w-8 h-8 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-4xl font-black text-white tracking-tight mb-3">Radar La Guardia</h1>
                <p class="text-lg text-sky-200/80 font-medium leading-relaxed max-w-sm mx-auto">
                    Tu comunidad, reportando en tiempo real
                </p>
                <div class="mt-8 flex items-center justify-center gap-6 text-sky-300/60 text-sm font-medium">
                    <span class="inline-flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        En vivo
                    </span>
                    <span>•</span>
                    <span>📍 Reportes ciudadanos</span>
                    <span>•</span>
                    <span>🗺️ Mapa interactivo</span>
                </div>
            </div>
        </div>

        {{-- RIGHT PANEL — Form Area --}}
        <div
            class="w-full lg:w-1/2 auth-bg lg:bg-none lg:bg-white flex items-center justify-center p-6 md:p-10 relative">

            {{-- Mobile: radar pulse background (hidden on desktop where left panel shows it) --}}
            <div
                class="lg:hidden absolute inset-0 flex items-center justify-center pointer-events-none overflow-hidden">
                <div class="radar-pulse"></div>
                <div class="radar-pulse"></div>
                <div class="radar-pulse"></div>
            </div>

            {{-- Form Container --}}
            <div class="w-full max-w-md relative z-10">
                {{-- Mobile glassmorphism card --}}
                <div
                    class="glass-card lg:bg-transparent lg:backdrop-blur-none rounded-3xl p-8 lg:p-0 shadow-2xl lg:shadow-none border border-white/20 lg:border-0">

                    {{-- Logo (mobile only — desktop has it in left panel) --}}
                    <div class="lg:hidden flex flex-col items-center mb-8">
                        <div
                            class="w-16 h-16 rounded-2xl bg-[#0f172a] flex items-center justify-center mb-4 shadow-lg shadow-slate-900/30">
                            <svg class="w-9 h-9 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                            </svg>
                        </div>
                        <h1 class="text-2xl font-black text-white tracking-tight">Radar La Guardia</h1>
                        <p class="text-sm text-sky-200/70 font-medium mt-1">Tu comunidad, reportando en tiempo real</p>
                    </div>

                    {{-- Desktop logo --}}
                    <div class="hidden lg:flex items-center gap-3 mb-8">
                        <div class="w-11 h-11 rounded-xl bg-[#0f172a] flex items-center justify-center shadow-sm">
                            <svg class="w-6 h-6 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                            </svg>
                        </div>
                        <span class="text-xl font-black text-slate-800 tracking-tight">Radar</span>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>

    @fluxScripts
</body>

</html>
