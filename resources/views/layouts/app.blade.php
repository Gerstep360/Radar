<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        @include('partials.head')
        <style>
            body { overscroll-behavior-y: none; background-color: #f8fafc; } /* Fondo gris muy suave */
            .pb-safe { padding-bottom: env(safe-area-inset-bottom); }
            .no-scrollbar::-webkit-scrollbar { display: none; }
            
            /* Sombra suave estilo iOS */
            .shadow-apple { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
            .shadow-float { box-shadow: 0 8px 30px rgba(0,0,0,0.12); }
        </style>
    </head>
    <body class="min-h-screen font-sans antialiased h-full overflow-hidden flex flex-col lg:flex-row text-slate-800">
        
        {{-- SIDEBAR DESKTOP (Sin cambios, solo oculto en móvil) --}}
        <flux:sidebar sticky stashable class="hidden lg:block border-e border-zinc-200 bg-white w-72 shrink-0 z-50">
            {{-- ... contenido sidebar desktop ... --}}
            <div class="p-4 font-bold text-xl">Radar.io</div>
        </flux:sidebar>

        {{-- ÁREA PRINCIPAL --}}
        <main class="flex-1 relative h-full w-full overflow-hidden">
            {{ $slot }}
        </main>

        {{-- BOTTOM NAVIGATION (MÓVIL) --}}
        <div class="lg:hidden fixed bottom-0 left-0 right-0 z-[100] pb-safe bg-white border-t border-slate-100 shadow-[0_-5px_20px_rgba(0,0,0,0.03)]">
            <div class="flex items-end justify-between px-8 h-[70px] pb-3 relative">
                
                {{-- 1. Explorar --}}
                <a href="{{ route('denuncias.index') }}" wire:navigate class="flex flex-col items-center gap-1 w-16 group">
                    <div class="p-2 rounded-xl transition-colors {{ request()->routeIs('denuncias.index') ? 'bg-blue-50 text-blue-600' : 'text-slate-400' }}">
                        {{-- Icono Mapa Sólido --}}
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M15 19l-6-2.118 6-2.118V19zM15 5v8l-6 2.118V7l6-2z"/><path d="M3.508 6.726c0-.85.908-1.4 1.658-1.006l6.16 3.236A1.25 1.25 0 0 1 12 9.992v8.282c0 .85-.908 1.4-1.658 1.006l-6.16-3.236A1.25 1.25 0 0 1 3.508 15V6.726zM20.492 6.726c0-.85-.908-1.4-1.658-1.006l-6.16 3.236A1.25 1.25 0 0 0 12 9.992v8.282c0 .85.908 1.4 1.658 1.006l6.16-3.236A1.25 1.25 0 0 0 20.492 15V6.726z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold {{ request()->routeIs('denuncias.index') ? 'text-blue-600' : 'text-slate-400' }}">Explorar</span>
                </a>

                {{-- 2. BOTÓN FLOTANTE (+) --}}
                {{-- Este es el botón negro grande que sobresale --}}
                <div class="absolute left-1/2 -translate-x-1/2 -top-6">
                    <button x-data @click="$dispatch('open-radar-modal')" 
                        class="h-16 w-16 bg-[#0f172a] text-white rounded-3xl flex items-center justify-center shadow-float active:scale-95 transition-transform hover:rotate-90 duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                    </button>
                </div>

                {{-- 3. Perfil --}}
                <a href="{{ route('profile.edit') }}" wire:navigate class="flex flex-col items-center gap-1 w-16 group">
                    <div class="p-2 rounded-xl transition-colors {{ request()->routeIs('profile.edit') ? 'bg-blue-50 text-blue-600' : 'text-slate-400' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                    </div>
                    <span class="text-[10px] font-bold {{ request()->routeIs('profile.edit') ? 'text-blue-600' : 'text-slate-400' }}">Perfil</span>
                </a>

            </div>
        </div>

        @fluxScripts
    </body>
</html>