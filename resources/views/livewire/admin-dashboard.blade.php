<div>
    {{-- ===== TOPBAR ===== --}}
    <div class="bg-white border-b border-slate-100 shadow-sm z-40 sticky top-0">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 py-4">
                {{-- Título --}}
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-blue-500 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-slate-800 leading-none">Centro de Comando</h1>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Radar La Guardia • Gestión Operativa</p>
                    </div>

                    {{-- Global Subtle Sync Indicator --}}
                    <div wire:loading.delay class="ml-4 flex items-center gap-2 px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full border border-indigo-100 animate-pulse">
                        <div class="animate-spin h-3 w-3 border-2 border-indigo-500 border-t-transparent rounded-full"></div>
                        <span class="text-[9px] font-black uppercase tracking-widest">Sincronizando...</span>
                    </div>
                </div>

                {{-- Navegación de Pestañas y Perfil --}}
                <div class="flex items-center gap-6">
                    <div class="flex space-x-1 bg-slate-100 p-1 rounded-xl">
                        <button wire:click="setTab('overview')"
                                class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ $currentTab === 'overview' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                                Resumen
                            </div>
                        </button>

                        <button wire:click="setTab('reports-list')"
                                class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ $currentTab === 'reports-list' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 002-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                                Denuncias
                            </div>
                        </button>

                        <button wire:click="setTab('map')"
                                class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ $currentTab === 'map' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                Mapa
                            </div>
                        </button>

                        @if(auth()->user()->isAdmin())
                        <button wire:click="setTab('users-list')"
                                class="px-4 py-2 text-sm font-semibold rounded-lg transition-all {{ $currentTab === 'users-list' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                Usuarios
                            </div>
                        </button>
                        @endif

                        <a href="{{ route('denuncias.index') }}"
                           class="px-4 py-2 text-sm font-semibold rounded-lg transition-all text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 border border-transparent hover:border-indigo-100 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Radar Global
                        </a>
                    </div>

                    {{-- Perfil de Usuario --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="flex items-center gap-3 p-1 rounded-2xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-100">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 font-bold shadow-inner uppercase">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="hidden md:block text-left mr-2">
                                <p class="text-xs font-black text-slate-800 leading-none">{{ auth()->user()->name }}</p>
                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">
                                    {{ auth()->user()->isAdmin() ? 'Administrador' : (auth()->user()->isModerator() ? 'Moderador' : 'Usuario') }}
                                </p>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-3 w-56 rounded-[1.5rem] bg-white shadow-2xl shadow-indigo-500/10 border border-slate-100 py-3 z-50 overflow-hidden"
                             style="display: none;">
                            
                            <div class="px-4 py-2 border-b border-slate-50 mb-2">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Sesión de</p>
                                <p class="text-xs font-black text-slate-800 truncate">{{ auth()->user()->email }}</p>
                            </div>

                            <button wire:click="logout" 
                                    class="w-full flex items-center gap-3 px-5 py-3 text-sm font-bold text-rose-500 hover:bg-rose-50 transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                Cerrar Sesión
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Contenido Dinámico según pestaña activa (Always mounted to preserve JS state) --}}
    <div class="w-full">
        <div class="{{ $currentTab === 'overview' ? 'block' : 'hidden' }}">
            <livewire:admin.overview wire:key="tab-overview" />
        </div>
        <div class="{{ $currentTab === 'reports-list' ? 'block' : 'hidden' }}">
            <livewire:admin.reports-list wire:key="tab-reports-list" />
        </div>
        <div class="{{ $currentTab === 'map' ? 'block' : 'hidden' }}">
            <livewire:admin.map wire:key="tab-map" />
        </div>
        @if(auth()->user()->isAdmin())
        <div class="{{ $currentTab === 'users-list' ? 'block' : 'hidden' }}">
            <livewire:admin.users-list wire:key="tab-users-list" />
        </div>
        @endif
    </div>

    @if($showReportModal && $selectedReport)
    <div class="fixed inset-0 z-[1000] overflow-y-auto flex items-center justify-center p-4">
        {{-- Fondo Ultra-Blur --}}
        <div class="fixed inset-0 bg-slate-900/95 backdrop-blur-xl transition-opacity animate-in fade-in duration-500" wire:click="closeModal"></div>
        
        {{-- Contenedor del Modal --}}
        <div class="relative bg-white w-full max-w-7xl rounded-[3rem] shadow-[0_50px_100px_rgba(0,0,0,0.6)] border border-white/20 overflow-hidden transform transition-all animate-in fade-in zoom-in slide-in-from-bottom-10 duration-500 flex flex-col lg:flex-row min-h-[700px] max-h-[92vh]">
            
            {{-- Botón Cerrar Flotante (Esquina Superior) --}}
            <button wire:click="closeModal" 
                    class="absolute top-6 right-6 z-[100] w-14 h-14 bg-white/10 hover:bg-rose-500 backdrop-blur-md rounded-2xl flex items-center justify-center transition-all hover:scale-110 active:scale-90 group border border-white/20 shadow-2xl">
                <svg class="w-8 h-8 text-white transition-transform group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- PANEL IZQUIERDO: CONTENIDO DE LA DENUNCIA (7 COL) --}}
            <div class="lg:w-7/12 flex flex-col bg-slate-50 relative overflow-y-auto custom-scrollbar border-r border-slate-200">
                {{-- Galería de Imágenes Premium --}}
                <div class="relative h-[500px] bg-slate-900 overflow-hidden group">
                    @if($selectedReport->media->count() > 0)
                        <div x-data="{ current: 0, total: {{ $selectedReport->media->count() }} }" class="h-full w-full">
                            @foreach($selectedReport->media as $index => $media)
                                <img src="{{ str_starts_with($media->file_path, 'http') ? $media->file_path : Storage::url($media->file_path) }}" 
                                     x-show="current === {{ $index }}"
                                     class="w-full h-full object-cover animate-fade-in transition-all duration-700"
                                     alt="Evidencia">
                            @endforeach
                            
                            {{-- Controles de Galería --}}
                            @if($selectedReport->media->count() > 1)
                                <div class="absolute inset-x-8 bottom-8 flex justify-between items-center z-10">
                                    <div class="flex gap-2 bg-black/40 backdrop-blur-md p-2 rounded-full border border-white/10">
                                        @foreach($selectedReport->media as $index => $media)
                                            <button @click="current = {{ $index }}" 
                                                    class="w-2.5 h-2.5 rounded-full transition-all border border-white/30"
                                                    :class="current === {{ $index }} ? 'bg-white scale-125' : 'bg-white/30 hover:bg-white/60'"></button>
                                        @endforeach
                                    </div>
                                    <span class="px-4 py-1.5 bg-black/40 backdrop-blur-md rounded-full text-[10px] font-black text-white/80 uppercase tracking-widest border border-white/10" x-text="(current + 1) + ' / ' + total"></span>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="h-full w-full flex flex-col items-center justify-center text-slate-700 gap-6 opacity-30">
                            <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-xs font-black uppercase tracking-[0.3em]">Evidencia no disponible</p>
                        </div>
                    @endif

                    {{-- Badge de Estado Overlay --}}
                    <div class="absolute top-8 left-8">
                         @php
                            $stColor = match($selectedReport->status) {
                                'pendiente' => 'bg-rose-500',
                                'en_revision' => 'bg-blue-500',
                                'atendido' => 'bg-emerald-500',
                                'desestimado' => 'bg-slate-500',
                                default => 'bg-indigo-500'
                            };
                        @endphp
                        <span class="{{ $stColor }} px-6 py-2 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] text-white shadow-2xl animate-pulse">
                            ● {{ strtoupper($selectedReport->status) }}
                        </span>
                    </div>
                </div>

                {{-- Cuerpo de la Información --}}
                <div class="p-12 space-y-10">
                    <div>
                        <div class="flex items-center gap-3 text-indigo-600 font-black text-xs uppercase tracking-[0.25em] mb-4">
                            <div class="w-10 h-1 rounded-full bg-indigo-600"></div>
                            {{ $selectedReport->category->name }}
                        </div>
                        <h2 class="text-5xl font-black text-slate-900 leading-[1.1] mb-6">{{ $selectedReport->title }}</h2>
                        
                        <div class="flex flex-wrap gap-8 text-[11px] font-black text-slate-400 uppercase tracking-widest">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-slate-200/50 flex items-center justify-center text-slate-500">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                {{ $selectedReport->created_at->format('d M, Y • H:i') }}
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-slate-200/50 flex items-center justify-center text-slate-500">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                TRAMITE #{{ $selectedReport->id }}
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2.5rem] p-10 border border-slate-200/60 shadow-sm relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50/50 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
                        <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-6 relative z-10">Relato Ciudadano</h4>
                        <p class="text-lg text-slate-700 leading-relaxed font-medium relative z-10 italic">
                            "{{ $selectedReport->description }}"
                        </p>
                    </div>

                    <div class="flex items-center justify-between p-7 bg-slate-900 rounded-[2rem] shadow-2xl shadow-slate-300/50 text-white overflow-hidden relative group border-4 border-slate-800">
                         <div class="absolute top-0 right-0 w-48 h-48 bg-blue-500/10 rounded-full -mr-10 -mt-20 transition-transform group-hover:scale-125 duration-700"></div>
                         <div class="flex items-center gap-5 relative z-10">
                            <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20 shadow-inner">
                                <svg class="w-7 h-7 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-blue-400 mb-1">Localización GPS</p>
                                <p class="text-xl font-black tracking-tight">Ruta en Vivo</p>
                            </div>
                         </div>
                         <a href="https://www.google.com/maps/dir/?api=1&destination={{ $selectedReport->latitude }},{{ $selectedReport->longitude }}" 
                            target="_blank"
                            class="px-8 py-5 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-[0_10px_25px_rgba(37,99,235,0.4)] transition-all active:scale-95 relative z-10 border-b-4 border-blue-800">
                             Navegar Ahora
                         </a>
                    </div>
                </div>
            </div>

            {{-- PANEL DERECHO: SIDEBAR DE RESOLUCIÓN (5 COL) --}}
            <div class="lg:w-5/12 p-10 bg-white flex flex-col space-y-8 overflow-y-auto custom-scrollbar">
                
                {{-- Reportero Compact --}}
                <div class="flex items-center gap-5 p-1">
                    <div class="w-14 h-14 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-300">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/></svg>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Reportado por</p>
                        <p class="text-lg font-black text-slate-800">{{ $selectedReport->user->name }}</p>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="text-[9px] font-black text-indigo-400 uppercase tracking-[0.1em]">{{ $selectedReport->votes_count ?? 0 }} ciudadanos apoyan esto</span>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-50">

                {{-- FORMULARIO DE RESOLUCIÓN --}}
                <div class="flex-1 flex flex-col space-y-8">
                    <div class="flex items-center justify-between">
                        <h4 class="text-[11px] font-black text-indigo-600 uppercase tracking-[0.3em]">Resolución Operativa</h4>
                        <div class="flex gap-2">
                            @if (session()->has('success'))
                                <span class="text-[9px] bg-emerald-500 text-white px-3 py-1 rounded-full font-black uppercase tracking-widest animate-in fade-in zoom-in duration-300">¡Actualizado!</span>
                            @endif
                            @if (session()->has('error'))
                                <span class="text-[9px] bg-rose-500 text-white px-3 py-1 rounded-full font-black uppercase tracking-widest animate-in fade-in zoom-in duration-300">{{ session('error') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Estado de la Denuncia</label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach(['pendiente', 'en_revision', 'atendido', 'desestimado'] as $st)
                                <button wire:click="$set('newStatus', '{{ $st }}')" 
                                        type="button"
                                        class="py-4 px-5 rounded-2xl border-2 transition-all text-xs font-black uppercase tracking-wider flex items-center justify-center gap-3 shadow-md 
                                        {{ $newStatus === $st 
                                            ? 'border-indigo-600 bg-indigo-600 text-white shadow-indigo-200' 
                                            : 'border-slate-200 bg-slate-50 text-slate-600 hover:border-slate-400' }}">
                                    @php
                                        $dotCol = match($st) {
                                            'pendiente' => $newStatus === $st ? 'bg-white' : 'bg-rose-500',
                                            'en_revision' => $newStatus === $st ? 'bg-white' : 'bg-blue-500',
                                            'atendido' => $newStatus === $st ? 'bg-white' : 'bg-emerald-500',
                                            'desestimado' => $newStatus === $st ? 'bg-white' : 'bg-slate-400',
                                            default => 'bg-indigo-500'
                                        };
                                    @endphp
                                    <span class="w-2.5 h-2.5 rounded-full {{ $dotCol }} shadow-sm"></span>
                                    {{ str_replace('_', ' ', $st) }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-4 flex-1">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Respuesta del Administrador</label>
                        <div class="relative h-[220px]">
                            <textarea wire:model="adminComment" 
                                placeholder="Escribe aquí la respuesta oficial para los ciudadanos..." 
                                class="w-full h-full rounded-3xl border-2 border-slate-200 bg-slate-50 p-7 text-sm font-bold text-slate-800 focus:bg-white focus:border-indigo-600 transition-all focus:ring-8 focus:ring-indigo-100 shadow-inner resize-none leading-relaxed"></textarea>
                        </div>
                    </div>

                    <div class="pt-8 border-t-2 border-slate-100">
                        <button wire:click="saveReportResponse" 
                                wire:loading.attr="disabled"
                                class="w-full py-6 rounded-3xl bg-indigo-600 hover:bg-indigo-700 text-white shadow-2xl shadow-indigo-500/30 flex items-center justify-center gap-4 transition-all active:scale-[0.97] group overflow-hidden relative border-b-8 border-indigo-900">
                            
                            <div class="flex items-center gap-4 relative z-10">
                                <span class="text-xs font-black uppercase tracking-[0.3em]" wire:loading.remove wire:target="saveReportResponse">Guardar Cambios</span>
                                <span class="text-xs font-black uppercase tracking-[0.3em]" wire:loading wire:target="saveReportResponse">Procesando...</span>
                                
                                <div wire:loading wire:target="saveReportResponse">
                                    <div class="animate-spin h-5 w-5 border-3 border-white border-t-transparent rounded-full"></div>
                                </div>
                                <svg wire:loading.remove wire:target="saveReportResponse" class="w-6 h-6 transition-transform group-hover:translate-x-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                            </div>

                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Componentes para Previsualización en el Mapa --}}
    <x-radar.info-point />
    <x-radar.mod-panel />
</div>
