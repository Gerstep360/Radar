<x-app-layout>
    <div x-data="{ 
            liked: {{ $denuncia->has_voted ? 'true' : 'false' }}, 
            count: {{ $denuncia->votes_count ?? 0 }},
            isLiking: false,
            scrolled: false,
            async toggleLike() {
                if (this.isLiking) return;
                this.isLiking = true;
                
                // Optimistic UI update
                this.liked = !this.liked;
                this.count += this.liked ? 1 : -1;
                
                // Animation trigger
                this.$refs.heartIcon.classList.add('scale-150', 'text-rose-500');
                setTimeout(() => this.$refs.heartIcon.classList.remove('scale-150'), 200);

                try {
                    await axios.post('{{ route('denuncias.vote', $denuncia->id) }}');
                } catch (error) {
                    // Revert on error
                    this.liked = !this.liked;
                    this.count += this.liked ? 1 : -1;
                }
                this.isLiking = false;
            }
        }"
        @scroll.window="scrolled = (window.pageYOffset > 50)"
        class="pb-24 bg-white min-h-screen relative w-full h-full overflow-y-auto no-scrollbar"
    >
        @php
            $hasMedia = $denuncia->media->count() > 0;
            $coverImage = $hasMedia ? $denuncia->media->first()->url : 'https://images.unsplash.com/photo-1541888045558-750567a14e68?q=80&w=800&auto=format&fit=crop';
        @endphp

        {{-- Floating Transparent Header --}}
        <div :class="scrolled ? 'bg-white/90 backdrop-blur-md shadow-sm text-black' : 'bg-transparent text-white'" 
             class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 px-4 h-16 flex items-center justify-between">
            <a href="{{ route('denuncias.index') }}" 
               class="w-10 h-10 flex items-center justify-center rounded-full transition-colors"
               :class="scrolled ? 'bg-slate-100 hover:bg-slate-200' : 'bg-black/20 backdrop-blur-md hover:bg-black/40 border border-white/20'">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>
            
            <div class="font-bold text-sm truncate max-w-[200px] transition-opacity duration-300"
                 :class="scrolled ? 'opacity-100' : 'opacity-0'">
                {{ $denuncia->title }}
            </div>
            
            <button class="w-10 h-10 flex items-center justify-center rounded-full transition-colors"
                    :class="scrolled ? 'bg-slate-100 hover:bg-slate-200' : 'bg-black/20 backdrop-blur-md hover:bg-black/40 border border-white/20'">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                </svg>
            </button>
        </div>

        {{-- Immersive Cover Image --}}
        <div class="relative w-full h-[350px] lg:h-[450px]">
            <img src="{{ $coverImage }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-black/60"></div>
            
            {{-- Floating Category Badge on Image --}}
            <div class="absolute bottom-6 left-5 flex items-center gap-2">
                <span class="px-3 py-1 bg-white/20 backdrop-blur-md border border-white/30 text-white font-bold text-xs rounded-full uppercase tracking-wider shadow-lg">
                    {{ $denuncia->category->name }}
                </span>
                
                @php
                    $statusData = match ($denuncia->status) {
                        'pendiente' => ['bg' => 'bg-amber-400', 'text' => 'text-black', 'label' => 'Pendiente'],
                        'en_revision' => ['bg' => 'bg-blue-500', 'text' => 'text-white', 'label' => 'En revisión'],
                        'atendido' => ['bg' => 'bg-green-500', 'text' => 'text-white', 'label' => 'Resuelto'],
                        default => ['bg' => 'bg-slate-400', 'text' => 'text-white', 'label' => ucfirst($denuncia->status)],
                    };
                @endphp
                <span class="px-3 py-1 flex items-center gap-1.5 {{ $statusData['bg'] }} {{ $statusData['text'] }} font-bold text-xs rounded-full uppercase tracking-wider shadow-lg">
                    <span class="w-1.5 h-1.5 rounded-full {{ $statusData['text'] === 'text-white' ? 'bg-white' : 'bg-black' }} animate-pulse"></span>
                    {{ $statusData['label'] }}
                </span>
            </div>
        </div>

        {{-- Content Body (Overlapping rounded card) --}}
        <div class="relative -mt-4 bg-white rounded-t-3xl pt-8 px-5 lg:max-w-3xl lg:mx-auto shadow-[0_-8px_20px_rgba(0,0,0,0.08)]">
            
            {{-- Drag Handle --}}
            <div class="absolute top-3 left-1/2 -translate-x-1/2 w-12 h-1.5 bg-slate-200 rounded-full"></div>

            <div class="flex items-start justify-between gap-4 mb-4">
                <h1 class="text-2xl font-black text-black leading-tight tracking-tight">{{ $denuncia->title }}</h1>
                
                {{-- Interactive Vote/Like Button --}}
                <button @click="toggleLike" 
                        class="shrink-0 flex flex-col items-center justify-center p-2 rounded-2xl transition-colors active:scale-95 touch-manipulation"
                        :class="liked ? 'bg-rose-50 shadow-[0_4px_12px_rgba(244,63,94,0.15)] text-rose-500' : 'bg-slate-50 text-slate-400'">
                    <svg x-ref="heartIcon" fill="currentColor" viewBox="0 0 24 24" class="w-7 h-7 transition-all duration-300"
                         :class="liked ? 'text-rose-500' : 'text-slate-300'">
                        <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
                    </svg>
                    <span class="text-xs font-bold mt-1" x-text="count"></span>
                </button>
            </div>

            {{-- User & Date Row --}}
            <div class="flex items-center justify-between py-4 border-y border-slate-100 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-sky-400 to-indigo-500 flex items-center justify-center text-white font-bold text-sm shadow-md">
                        {{ substr($denuncia->user->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="text-[13px] font-bold text-black">{{ $denuncia->user->name }}</div>
                        <div class="text-xs font-semibold text-slate-500">Repartidor activo</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-[13px] font-bold text-slate-800">{{ $denuncia->created_at->diffForHumans() }}</div>
                    <div class="text-xs font-semibold text-slate-400">{{ $denuncia->created_at->format('d M, Y') }}</div>
                </div>
            </div>

            {{-- ============================================================
                 PANEL DE GESTIÓN (Solo admin / moderador)
                 ============================================================ --}}
            @if(auth()->user()->hasAnyRole(['admin', 'moderador']))
            <div
                x-data="{
                    currentStatus: '{{ $denuncia->status }}',
                    loading: false,
                    success: false,
                    async changeStatus(newStatus) {
                        if (this.currentStatus === newStatus || this.loading) return;
                        this.loading = true;
                        try {
                            const res = await fetch('{{ route('denuncias.status', $denuncia) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                    'X-HTTP-Method-Override': 'PATCH',
                                },
                                body: JSON.stringify({ status: newStatus, _method: 'PATCH' })
                            });
                            if (res.ok) {
                                this.currentStatus = newStatus;
                                this.success = true;
                                setTimeout(() => this.success = false, 2000);
                            }
                        } finally {
                            this.loading = false;
                        }
                    },
                    getLabel(s) {
                        const m = {
                            pendiente: 'Pendiente', en_revision: 'En Proceso',
                            atendido: 'Resuelto', desestimado: 'Rechazado',
                            resuelto: 'Resuelto', rechazado: 'Rechazado',
                            en_proceso: 'En Proceso',
                        };
                        return m[s] || s;
                    },
                    getBadgeClass(s) {
                        if (['pendiente','pending'].includes(s))
                            return 'bg-amber-100 text-amber-700 border-amber-200';
                        if (['en_revision','in_progress','en_proceso'].includes(s))
                            return 'bg-blue-100 text-blue-700 border-blue-200';
                        if (['atendido','resolved','resuelto'].includes(s))
                            return 'bg-emerald-100 text-emerald-700 border-emerald-200';
                        return 'bg-slate-100 text-slate-500 border-slate-200';
                    }
                }"
                class="mb-6 bg-indigo-50 border border-indigo-100 rounded-3xl p-4"
            >
                {{-- Header del panel --}}
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-indigo-500 rounded-lg flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-black text-indigo-700 uppercase tracking-widest">Gestión de Moderador</span>
                    </div>

                    {{-- Badge estado actual (animado) --}}
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black border transition-all"
                        :class="getBadgeClass(currentStatus)"
                    >
                        <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
                        <span x-text="getLabel(currentStatus)"></span>
                    </span>
                </div>

                {{-- Botones de estado --}}
                <div class="grid grid-cols-2 gap-2">
                    {{-- Pendiente --}}
                    <button
                        @click="changeStatus('pendiente')"
                        :disabled="loading"
                        class="flex items-center gap-2 px-3 py-2.5 rounded-2xl border text-sm font-bold transition-all active:scale-95 disabled:opacity-50"
                        :class="currentStatus === 'pendiente'
                            ? 'bg-amber-400 text-black border-amber-400 shadow-md shadow-amber-200'
                            : 'bg-white text-amber-600 border-amber-200 hover:bg-amber-50'"
                    >
                        <span class="text-base">⏳</span>
                        <span>Pendiente</span>
                    </button>

                    {{-- En Proceso --}}
                    <button
                        @click="changeStatus('en_revision')"
                        :disabled="loading"
                        class="flex items-center gap-2 px-3 py-2.5 rounded-2xl border text-sm font-bold transition-all active:scale-95 disabled:opacity-50"
                        :class="['en_revision','in_progress','en_proceso'].includes(currentStatus)
                            ? 'bg-blue-500 text-white border-blue-500 shadow-md shadow-blue-200'
                            : 'bg-white text-blue-600 border-blue-200 hover:bg-blue-50'"
                    >
                        <span class="text-base">🔵</span>
                        <span>En Proceso</span>
                    </button>

                    {{-- Resuelto --}}
                    <button
                        @click="changeStatus('atendido')"
                        :disabled="loading"
                        class="flex items-center gap-2 px-3 py-2.5 rounded-2xl border text-sm font-bold transition-all active:scale-95 disabled:opacity-50"
                        :class="['atendido','resolved','resuelto'].includes(currentStatus)
                            ? 'bg-emerald-500 text-white border-emerald-500 shadow-md shadow-emerald-200'
                            : 'bg-white text-emerald-600 border-emerald-200 hover:bg-emerald-50'"
                    >
                        <span class="text-base">✅</span>
                        <span>Resuelto</span>
                    </button>

                    {{-- Rechazado --}}
                    <button
                        @click="changeStatus('desestimado')"
                        :disabled="loading"
                        class="flex items-center gap-2 px-3 py-2.5 rounded-2xl border text-sm font-bold transition-all active:scale-95 disabled:opacity-50"
                        :class="['desestimado','rejected','rechazado'].includes(currentStatus)
                            ? 'bg-slate-600 text-white border-slate-600 shadow-md shadow-slate-200'
                            : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                    >
                        <span class="text-base">❌</span>
                        <span>Rechazado</span>
                    </button>
                </div>

                {{-- Feedback de guardado --}}
                <div
                    x-show="success"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="mt-3 flex items-center gap-2 bg-emerald-50 border border-emerald-200 rounded-2xl px-4 py-2"
                >
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p class="text-xs font-bold text-emerald-700">Estado actualizado correctamente</p>
                </div>

                {{-- Spinner de carga --}}
                <div x-show="loading" class="mt-3 flex items-center justify-center gap-2 py-1">
                    <svg class="w-4 h-4 text-indigo-500 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span class="text-xs text-indigo-500 font-bold">Guardando...</span>
                </div>
            </div>
            @endif

            <p class="text-[15px] text-slate-700 leading-relaxed font-medium mb-8">
                {{ $denuncia->description }}
            </p>


            {{-- Location Card --}}
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-black flex items-center justify-center text-white">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0115 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-500">Ubicación exacta</div>
                        <div class="text-sm font-bold text-black">{{ number_format($denuncia->latitude, 4) }}, {{ number_format($denuncia->longitude, 4) }}</div>
                    </div>
                </div>
                <a href="https://maps.google.com/?q={{ $denuncia->latitude }},{{ $denuncia->longitude }}" target="_blank" class="px-4 py-2 bg-white rounded-lg text-xs font-bold text-black border border-slate-200 shadow-sm active:scale-95 transition-transform">
                    Ver mapa
                </a>
            </div>

            {{-- Full Gallery Grid --}}
            @if($hasMedia)
                <h3 class="font-black text-lg text-black mb-4">Evidencia ({{ $denuncia->media->count() }})</h3>
                <div class="grid grid-cols-2 gap-3 mb-8">
                    @foreach($denuncia->media as $foto)
                        <div onclick="openLightbox({{ $loop->index }})" class="relative aspect-square rounded-2xl overflow-hidden shadow-sm border border-slate-100 cursor-pointer active:scale-95 transition-transform group">
                            <img src="{{ $foto->url }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                    @endforeach
                </div>
            @endif
            
            <br><br>
        </div>

        {{-- Floating Action Bar (Sticky to bottom) --}}
        <div class="fixed bottom-0 left-0 right-0 p-4 bg-white/80 backdrop-blur-xl border-t border-slate-100 shadow-[0_-10px_40px_rgba(0,0,0,0.05)] z-40 lg:hidden">
            <div class="flex gap-3 max-w-[480px] mx-auto">
                {{-- Interactive Map Button --}}
                <a href="https://maps.google.com/?q={{ $denuncia->latitude }},{{ $denuncia->longitude }}" target="_blank"
                   class="flex-1 flex items-center justify-center gap-2 bg-black text-white px-5 py-3.5 rounded-2xl font-bold text-[15px] shadow-lg shadow-black/20 active:scale-95 transition-transform">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                         <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                         <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0115 0z" />
                    </svg>
                    Navegar ahí
                </a>
                
                {{-- Secondary Action --}}
                <button @click="$dispatch('open-share-modal')" class="w-14 shrink-0 flex items-center justify-center bg-slate-100 text-black rounded-2xl active:scale-95 transition-transform shadow-sm">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Alpine Lightbox --}}
        @if ($hasMedia)
        <div x-data="{
                isOpen: false,
                currentIndex: 0,
                photos: {{ json_encode($denuncia->media->pluck('url')) }},
                init() {
                    window.openLightbox = (i) => {
                        this.currentIndex = i;
                        this.isOpen = true;
                        document.body.style.overflow = 'hidden';
                    };
                },
                close() {
                    this.isOpen = false;
                    document.body.style.overflow = '';
                },
                next() { this.currentIndex = (this.currentIndex + 1) % this.photos.length; },
                prev() { this.currentIndex = (this.currentIndex - 1 + this.photos.length) % this.photos.length; }
            }"
            @keydown.escape.window="close"
            @keydown.arrow-right.window="if(isOpen) next()"
            @keydown.arrow-left.window="if(isOpen) prev()"
            x-show="isOpen"
            style="display: none;"
            x-transition.opacity.duration.300ms
            class="fixed inset-0 z-[100] bg-black/95 backdrop-blur-sm flex flex-col items-center justify-center pt-safe pb-safe"
        >
            <button @click="close" class="absolute top-6 right-6 text-white p-2 bg-white/10 rounded-full hover:bg-white/20 transition-colors z-10 backdrop-blur-md">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>

            <div class="relative w-full max-w-4xl h-full flex items-center justify-center outline-none" tabindex="0">
                <button @click.stop="prev" class="absolute left-4 z-10 p-3 bg-black/50 hover:bg-black text-white rounded-full backdrop-blur-md transition-colors">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                </button>

                <img :src="photos[currentIndex]" class="max-w-[100vw] max-h-[100vh] object-contain w-full h-full lg:rounded-2xl shadow-2xl transition-all duration-300" @click.stop="">

                <button @click.stop="next" class="absolute right-4 z-10 p-3 bg-black/50 hover:bg-black text-white rounded-full backdrop-blur-md transition-colors">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                </button>
            </div>

            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 px-4 py-1.5 bg-black/50 backdrop-blur-md text-white/90 text-xs font-bold tracking-widest rounded-full uppercase">
                <span x-text="currentIndex + 1"></span> / <span x-text="photos.length"></span>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
