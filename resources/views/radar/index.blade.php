<x-app-layout>
    <x-slot name="styles">
        <style>
            html, body { height: 100%; margin: 0; overflow: hidden; overscroll-behavior: none; }
            .leaflet-container { width: 100%; height: 100%; background: #f1f5f9; }
            .custom-scrollbar::-webkit-scrollbar { width: 0px; background: transparent; }
        </style>
    </x-slot>

    {{-- CONTENEDOR FULL-SCREEN --}}
    <div class="fixed inset-0 w-full h-full bg-slate-200 overflow-hidden">

        {{-- 1. MAPA --}}
        <x-map.radar-map :markers="$denuncias->items()" class="absolute inset-0 w-full h-full z-0" />

        {{-- 2. HUD SUPERIOR (mobile: left-4 right-4 | desktop: centrado) --}}
        <x-radar.live-status
            title="Radar La Guardia"
            :count="$denuncias->total()"
            icon="radar"
            class="absolute top-4 z-10 safe-area-top
                   left-4 right-4
                   lg:left-[200px] lg:right-auto lg:w-[340px]"
        />

        {{-- 3. NOTIFICACIONES --}}
        <x-radar.notifications position="top-right" :maxVisible="5" :duration="5000" />

        {{-- ============================================================
             4. PANEL FLOTANTE DESKTOP (top-left, solo lg+)
             Botón menú hamburguesa que abre/cierra un panel con controles
             ============================================================ --}}
        <div
            x-data="{ open: false }"
            class="hidden lg:block absolute top-4 left-4 z-[200]"
            @keydown.escape.window="open = false"
        >
            {{-- Botón toggle --}}
            <button
                @click="open = !open"
                class="w-12 h-12 bg-white/95 backdrop-blur-xl rounded-2xl shadow-lg border border-white/60 flex items-center justify-center text-slate-700 hover:bg-white hover:shadow-xl active:scale-95 transition-all"
                :class="open ? 'bg-indigo-600 text-white border-indigo-500 shadow-indigo-200' : ''"
                title="Menú de opciones"
            >
                {{-- Icono hamburgesa / X --}}
                <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Panel desplegable --}}
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                @click.outside="open = false"
                class="absolute top-14 left-0 w-72 bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/60 overflow-hidden"
            >
                {{-- Header del panel --}}
                <div class="px-5 py-4 bg-gradient-to-br from-indigo-600 to-blue-700">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-black text-white text-sm">Radar La Guardia</p>
                            <p class="text-indigo-200 text-[10px] font-bold">{{ $denuncias->total() }} reportes activos</p>
                        </div>
                    </div>
                </div>

                {{-- Acciones principales --}}
                <div class="p-3 space-y-1">
                    {{-- Crear Reporte --}}
                    <button
                        x-data
                        @click="$dispatch('open-radar-modal'); $root.open = false"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-indigo-50 text-slate-700 hover:text-indigo-700 transition-colors group text-left"
                    >
                        <div class="w-8 h-8 bg-slate-100 group-hover:bg-indigo-100 rounded-xl flex items-center justify-center transition-colors shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-sm">Crear Reporte</p>
                            <p class="text-[10px] text-slate-400">Reportar una incidencia nueva</p>
                        </div>
                    </button>

                    @if(auth()->user()->hasAnyRole(['admin', 'moderador']))
                    {{-- Panel Admin --}}
                    <a href="{{ route('admin.dashboard') }}"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-indigo-50 text-slate-700 hover:text-indigo-700 transition-colors group"
                    >
                        <div class="w-8 h-8 bg-indigo-100 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-indigo-700">Panel Admin</p>
                            <p class="text-[10px] text-slate-400">Gestión y estadísticas</p>
                        </div>
                        <svg class="w-4 h-4 text-slate-300 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    @endif

                    {{-- Ver Reportes (lista desplegable) --}}
                    <div x-data="{ listOpen: false }">
                        <button
                            @click="listOpen = !listOpen"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl hover:bg-slate-50 text-slate-700 transition-colors group text-left"
                        >
                            <div class="w-8 h-8 bg-slate-100 group-hover:bg-slate-200 rounded-xl flex items-center justify-center shrink-0 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-sm">Ver Reportes</p>
                                <p class="text-[10px] text-slate-400">{{ $denuncias->total() }} en total</p>
                            </div>
                            <svg class="w-4 h-4 text-slate-300 transition-transform" :class="listOpen ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>

                        <div x-show="listOpen" x-transition class="mt-1 mx-2 bg-slate-50 rounded-2xl overflow-hidden max-h-60 overflow-y-auto">
                            @forelse($denuncias as $d)
                            <div
                                class="px-4 py-2.5 hover:bg-white cursor-pointer flex items-center gap-2 transition-colors border-b border-slate-100 last:border-0"
                                onclick="desktopFlyTo({{ (float)$d->latitude }}, {{ (float)$d->longitude }}, {{ $d->id }}, {{ json_encode($d->title ?? $d->titulo ?? '') }}, {{ json_encode(Str::limit($d->description ?? $d->descripcion ?? '', 80)) }}, {{ json_encode($d->status ?? $d->estado ?? 'pendiente') }}, {{ json_encode($d->category?->name ?? 'General') }})"
                            >
                                @php
                                    $st = $d->status ?? $d->estado ?? 'pendiente';
                                    $dot = match(true) {
                                        in_array($st, ['pendiente','pending']) => 'bg-amber-400',
                                        in_array($st, ['en_revision','in_progress','en_proceso']) => 'bg-blue-500',
                                        in_array($st, ['atendido','resolved','resuelto']) => 'bg-emerald-500',
                                        default => 'bg-slate-300',
                                    };
                                @endphp
                                <span class="w-2 h-2 rounded-full {{ $dot }} shrink-0"></span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-slate-700 truncate">{{ $d->title ?? $d->titulo ?? 'Sin título' }}</p>
                                    <p class="text-[10px] text-slate-400 truncate">{{ $d->category?->name ?? 'General' }}</p>
                                </div>
                                @if(auth()->user()->hasAnyRole(['admin', 'moderador']))
                                <form action="{{ route('denuncias.status', $d) }}" method="POST" onclick="event.stopPropagation()">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="text-[9px] py-0.5 pl-1 pr-4 rounded-lg border border-slate-200 bg-white font-bold cursor-pointer">
                                        <option value="pendiente" {{ in_array($st, ['pendiente','pending']) ? 'selected' : '' }}>⏳</option>
                                        <option value="en_revision" {{ in_array($st, ['en_revision','in_progress','en_proceso']) ? 'selected' : '' }}>🔵</option>
                                        <option value="atendido" {{ in_array($st, ['atendido','resolved','resuelto']) ? 'selected' : '' }}>✅</option>
                                        <option value="desestimado" {{ in_array($st, ['desestimado','rejected','rechazado']) ? 'selected' : '' }}>❌</option>
                                    </select>
                                </form>
                                @endif
                            </div>
                            @empty
                            <p class="px-4 py-3 text-xs text-slate-400 text-center">Sin reportes</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Separador --}}
                <div class="h-px bg-slate-100 mx-4"></div>

                {{-- Acciones secundarias --}}
                <div class="p-3 space-y-1">
                    <a href="{{ route('profile.edit') }}"
                        class="w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl hover:bg-slate-50 text-slate-600 hover:text-slate-800 transition-colors"
                    >
                        <div class="w-8 h-8 bg-slate-100 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                        </div>
                        <p class="font-bold text-sm">Mi Perfil</p>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl hover:bg-red-50 text-slate-600 hover:text-red-600 transition-colors"
                        >
                            <div class="w-8 h-8 bg-slate-100 rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                                </svg>
                            </div>
                            <p class="font-bold text-sm">Cerrar Sesión</p>
                        </button>
                    </form>
                </div>

                {{-- Footer: usuario actual --}}
                <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex items-center gap-2">
                    <div class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center text-[10px] font-black text-indigo-600 shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-700 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[9px] text-slate-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>
        </div>
        {{-- /PANEL FLOTANTE DESKTOP --}}

        {{-- 5. BOTTOM SHEET (mobile) --}}
        <x-radar.bottom-sheet :denuncias="$denuncias" />

        {{-- 6. PANEL MODERACIÓN (mobile/desktop) --}}
        <x-radar.mod-panel />

        {{-- 7. INFO POINT --}}
        <x-radar.info-point />

        {{-- 8. HUD INFERIOR (Mobile Navigation for Admins) --}}
        @if(auth()->user()->hasAnyRole(['admin', 'moderador']))
        <div class="lg:hidden absolute bottom-6 inset-x-4 z-[50]">
            <div class="bg-white/95 backdrop-blur-xl rounded-[2rem] shadow-2xl border border-white/60 p-2 flex items-center justify-around">
                <a href="{{ route('denuncias.index') }}" class="p-4 rounded-3xl transition-all {{ request()->routeIs('denuncias.index') ? 'bg-indigo-600 text-white' : 'text-slate-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                </a>

                <a href="{{ route('admin.dashboard') }}" class="p-4 rounded-3xl text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </a>

                <button x-data @click="$dispatch('open-radar-modal')" class="p-4 rounded-3xl bg-slate-900 text-white shadow-lg active:scale-95 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </button>
            </div>
        </div>
        @endif
    </div>

    {{-- MODAL CREAR --}}
    <x-radar.create-modal :categories="$categories" />

    <script>
        function desktopFlyTo(lat, lng, id, titulo, descripcion, estado, category) {
            if (isNaN(lat) || isNaN(lng)) return;
            window.dispatchEvent(new CustomEvent('fly-to-map', {
                detail: { id, lat, lng, titulo, descripcion, estado, votes_count: 0, category, has_voted: false }
            }));
        }
    </script>

</x-app-layout>