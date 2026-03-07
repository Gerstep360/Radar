<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-screen">

<head>
    @include('partials.head')
    <style>
        body {
            overscroll-behavior-y: none;
            background-color: #f8fafc;
        }

        .pb-safe {
            padding-bottom: env(safe-area-inset-bottom);
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .shadow-float {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
    </style>
</head>

<body class="min-h-screen font-sans antialiased flex flex-col text-slate-800 bg-[#f8fafc]">

    {{-- ÁREA PRINCIPAL (sin sidebar en PC, full-screen) --}}
    <main class="flex-1 relative w-full pb-20 lg:pb-0">
        {{ $slot }}
    </main>

    {{-- BOTTOM NAVIGATION (MÓVIL) --}}
    <div
        class="lg:hidden fixed bottom-0 left-0 right-0 z-[100] pb-safe bg-white border-t border-slate-100 shadow-[0_-5px_20px_rgba(0,0,0,0.03)]">
        <div class="flex items-end justify-around px-4 h-[70px] pb-3 relative">

            {{-- 1. Explorar --}}
            <a href="{{ route('denuncias.index') }}" class="flex flex-col items-center gap-1 w-16 group">
                <div class="p-2 rounded-xl transition-colors {{ request()->routeIs('denuncias.index') ? 'bg-blue-50 text-blue-600' : 'text-slate-400' }}">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M15 19l-6-2.118 6-2.118V19zM15 5v8l-6 2.118V7l6-2z" />
                        <path d="M3.508 6.726c0-.85.908-1.4 1.658-1.006l6.16 3.236A1.25 1.25 0 0 1 12 9.992v8.282c0 .85-.908 1.4-1.658 1.006l-6.16-3.236A1.25 1.25 0 0 1 3.508 15V6.726zM20.492 6.726c0-.85-.908-1.4-1.658-1.006l-6.16 3.236A1.25 1.25 0 0 0 12 9.992v8.282c0 .85.908 1.4 1.658 1.006l6.16-3.236A1.25 1.25 0 0 0 20.492 15V6.726z" />
                    </svg>
                </div>
                <span class="text-[10px] font-bold {{ request()->routeIs('denuncias.index') ? 'text-blue-600' : 'text-slate-400' }}">Explorar</span>
            </a>

            {{-- 2. BOTÓN CENTRAL (diferente según el rol) --}}
            <div class="absolute left-1/2 -translate-x-1/2 -top-6">
                @if(auth()->check() && auth()->user()->hasAnyRole(['admin', 'moderador']))
                    {{-- Admin: link directo al panel --}}
                    <a href="{{ route('admin.dashboard') }}"
                        class="h-16 w-16 bg-gradient-to-br from-indigo-600 to-blue-700 text-white rounded-3xl flex items-center justify-center shadow-float active:scale-95 transition-transform {{ request()->routeIs('admin.dashboard') ? 'ring-2 ring-offset-2 ring-indigo-500' : '' }}">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </a>
                @else
                    {{-- Usuario normal: abrir modal de reporte --}}
                    <button x-data @click="$dispatch('open-radar-modal')"
                        class="h-16 w-16 bg-[#0f172a] text-white rounded-3xl flex items-center justify-center shadow-float active:scale-95 transition-transform hover:rotate-90 duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                @endif
            </div>

            {{-- 3. Admin: Panel / Perfil (según rol) --}}
            @if(auth()->check() && auth()->user()->hasAnyRole(['admin', 'moderador']))
                {{-- Los admin ven: Explorar | Panel | Reportar --}}
                <button x-data @click="$dispatch('open-radar-modal')"
                    class="flex flex-col items-center gap-1 w-16 group text-slate-400 hover:text-blue-600 transition-colors">
                    <div class="p-2 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold">Nuevo</span>
                </button>
            @else
                {{-- Usuario normal: Explorar | Crear | Perfil --}}
                <a href="{{ route('profile.edit') }}" class="flex flex-col items-center gap-1 w-16 group">
                    <div class="p-2 rounded-xl transition-colors {{ request()->routeIs('profile.edit') ? 'bg-blue-50 text-blue-600' : 'text-slate-400' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold {{ request()->routeIs('profile.edit') ? 'text-blue-600' : 'text-slate-400' }}">Perfil</span>
                </a>
            @endif

        </div>
    </div>

    @fluxScripts

    {{-- Script de notificaciones nativas Web Push --}}
    @auth
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const vapidPublicKey = '{{ env("VAPID_PUBLIC_KEY") }}';
            
            if (!vapidPublicKey || vapidPublicKey.length === 0) {
                console.warn('VAPID_PUBLIC_KEY no configurada');
                return;
            }

            if ('serviceWorker' in navigator && 'PushManager' in window) {
                try {
                    const registration = await navigator.serviceWorker.register('/sw.js');
                    
                    if (Notification.permission === 'default') {
                        // Opcional: Podrías pedir permiso aquí directamente, 
                        // pero es mejor práctica hacerlo cuando el usuario hace clic en un botón.
                        // Para automatizarlo tras login (o de a poco para no asustar):
                        // const permission = await Notification.requestPermission();
                    }

                    if (Notification.permission === 'granted') {
                        // Check si ya está suscrito
                        const subscription = await registration.pushManager.getSubscription();
                        if (!subscription) {
                            const newSubscription = await registration.pushManager.subscribe({
                                userVisibleOnly: true,
                                applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
                            });

                            // Enviar al backend
                            await fetch('/push/subscribe', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify(newSubscription)
                            });
                        }
                    }
                } catch (error) {
                    console.error('Error con Web Push:', error);
                }
            }
        });

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/\-/g, '+')
                .replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
        
        // Función global para que la llames desde un botón si el usuario aún no aceptó
        window.requestWebPushPermission = async () => {
             const permission = await Notification.requestPermission();
             if (permission === 'granted') {
                 // recargar la página desencadenará la suscripción del DOMContentLoaded
                 window.location.reload();
             }
        }
    </script>
    @endauth
</body>

</html>
