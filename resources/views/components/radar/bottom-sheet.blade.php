@props(['denuncias'])

{{-- Form oculto para votos (se reutiliza con diferentes IDs) --}}
<form id="bottom-sheet-vote-form" method="POST" style="display: none;">
    @csrf
</form>

<div x-data="bottomSheet()" x-init="initSheet"
    class="absolute left-0 right-0 z-20 flex flex-col bg-white shadow-[0_-10px_40px_rgba(0,0,0,0.1)] rounded-t-[2rem] border-t border-slate-50 w-full"
    :style="`height: 92vh; bottom: 0; transform: translateY(${currentY}px); transition: ${isDragging ? 'none' : 'transform 0.4s cubic-bezier(0.32, 0.72, 0, 1)'}`"
    @touchstart="startDrag" @touchmove="onDrag" @touchend="endDrag">

    {{-- Handle --}}
    <div class="w-full flex justify-center pt-3 pb-1 cursor-pointer touch-none" @click="toggleState()">
        <div class="h-1.5 w-12 bg-slate-200 rounded-full transition-colors hover:bg-slate-300"></div>
    </div>

    {{-- Header --}}
    <div class="px-6 pb-3 pt-1 flex justify-between items-end bg-white rounded-t-[2rem]">
        <div>
            <h2 class="text-lg font-black text-slate-800 tracking-tight">Explorar Zona</h2>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wide">{{ $denuncias->count() }} Reportes</p>
        </div>
        <button
            class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1.5 rounded-lg active:scale-95 transition-transform"
            @click="snapTo('full')">
            Ver todos
        </button>
    </div>

    {{-- Lista --}}
    <div class="flex-1 px-4 pb-24 space-y-3 bg-slate-50/50 custom-scrollbar overscroll-none"
        :class="state === 'full' ? 'overflow-y-auto' : 'overflow-hidden pointer-events-none'"
        x-ref="scrollContainer">
        @forelse($denuncias as $denuncia)
            <div class="bg-white p-3 rounded-2xl shadow-sm border border-slate-100 flex gap-3 active:scale-[0.98] transition-transform cursor-pointer group hover:border-blue-100 relative pointer-events-auto"
                data-denuncia-id="{{ $denuncia->id }}" data-lat="{{ $denuncia->latitude }}"
                data-lng="{{ $denuncia->longitude }}" data-titulo="{{ $denuncia->title ?? ($denuncia->titulo ?? '') }}"
                data-descripcion="{{ Str::limit($denuncia->description ?? ($denuncia->descripcion ?? ''), 100) }}"
                data-estado="{{ $denuncia->status ?? ($denuncia->estado ?? 'pendiente') }}"
                data-category="{{ $denuncia->category?->name ?? 'General' }}" @click="handleClickCard($el)">

                {{-- Foto --}}
                <div
                    class="w-16 h-16 bg-slate-100 rounded-xl flex-shrink-0 overflow-hidden relative border border-slate-50">
                    @if ($denuncia->media->isNotEmpty() && $denuncia->media->first()->exists())
                        <img src="{{ Storage::url($denuncia->media->first()->url) }}" class="w-full h-full object-cover"
                            loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0 flex flex-col justify-center pr-10">
                    <h3 class="font-bold text-slate-800 text-sm truncate">
                        {{ $denuncia->title ?? ($denuncia->titulo ?? 'Sin título') }}</h3>
                    <p class="text-[10px] text-slate-500 line-clamp-1 mt-0.5">
                        {{ $denuncia->description ?? ($denuncia->descripcion ?? '') }}</p>
                    <div class="flex items-center justify-between mt-1.5">
                        <span
                            class="text-[9px] font-bold text-slate-400 bg-slate-50 px-1.5 py-0.5 rounded">{{ $denuncia->created_at->diffForHumans(null, true) }}</span>
                    </div>
                </div>

                {{-- BOTÓN VOTO DIRECTO --}}
                <button @click.stop="vote({{ $denuncia->id }}, $el)"
                    class="absolute right-3 bottom-3 p-2 rounded-full hover:bg-slate-50 active:scale-75 transition-all z-10 flex items-center gap-1 group/heart border border-transparent">
                    {{-- Icono Corazón (Rojo si votado, Gris si no) --}}
                    <svg class="w-4 h-4 transition-colors duration-300 {{ $denuncia->has_voted ? 'text-red-500 fill-current' : 'text-slate-300' }}"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                            clip-rule="evenodd" />
                    </svg>
                    {{-- Contador --}}
                    <span
                        class="text-xs font-bold transition-colors duration-300 {{ $denuncia->has_voted ? 'text-red-500' : 'text-slate-400' }}">
                        {{ $denuncia->votes_count ?? 0 }}
                    </span>
                </button>

            </div>
        @empty
            <div class="py-10 text-center text-slate-400 text-sm pointer-events-auto">No hay reportes cercanos</div>
        @endforelse
    </div>
</div>

@once
    <script>
        if (typeof window.bottomSheet === 'undefined') {
            window.bottomSheet = function() {
                return {
                    state: 'half',
                    isDragging: false,
                    startY: 0,
                    currentY: 0,
                    startYWhenDragStarted: 0,
                    positions: {
                        min: 0,
                        half: 0,
                        full: 0
                    },
                    screenH: 0,

                    initSheet() {
                        this.calculatePositions();
                        this.currentY = this.positions[this.state];

                        window.addEventListener('resize', () => {
                            this.calculatePositions();
                            if (!this.isDragging) this.currentY = this.positions[this.state];
                        });

                        // Eventos externos (Map Click, Info Point, etc)
                        window.addEventListener('minimize-bottom-sheet', () => {
                            if (this.state !== 'min') this.snapTo('min');
                        });

                        window.addEventListener('close-info-point', () => {
                            if (this.state === 'min') this.snapTo('half');
                        });

                        window.addEventListener('vote-updated', (e) => {
                            const card = document.querySelector(`[data-denuncia-id="${e.detail.report_id}"]`);
                            if (card) {
                                const icon = card.querySelector('button svg');
                                const countSpan = card.querySelector('button span');
                                if (countSpan) {
                                    countSpan.textContent = e.detail.votes_count;
                                    countSpan.classList.add('scale-125');
                                    setTimeout(() => countSpan.classList.remove('scale-125'), 200);
                                }
                                // Sync heart icon style
                                if (icon && typeof e.detail.voted !== 'undefined') {
                                    if (e.detail.voted) {
                                        icon.classList.remove('text-slate-300');
                                        icon.classList.add('text-red-500', 'fill-current');
                                        if (countSpan) {
                                            countSpan.classList.remove('text-slate-400');
                                            countSpan.classList.add('text-red-500');
                                        }
                                    } else {
                                        icon.classList.remove('text-red-500', 'fill-current');
                                        icon.classList.add('text-slate-300');
                                        if (countSpan) {
                                            countSpan.classList.remove('text-red-500');
                                            countSpan.classList.add('text-slate-400');
                                        }
                                    }
                                }
                            }
                        });

                        this.$watch('state', value => {
                            window.bottomSheetState = value;
                            window.dispatchEvent(new CustomEvent('bottom-sheet-state', {
                                detail: {
                                    state: value
                                }
                            }));
                        });
                    },

                    calculatePositions() {
                        this.screenH = window.innerHeight;
                        const panelH = this.screenH * 0.92; // 92vh
                        this.positions = {
                            'min': panelH - 120, // offset leaving 120px visible
                            'half': panelH - (this.screenH * 0.45), // 45vh visible
                            'full': 0 // 100% of 92vh visible
                        };
                    },

                    snapTo(newState) {
                        this.state = newState;
                        this.currentY = this.positions[newState];
                        if (newState !== 'full' && this.$refs.scrollContainer) {
                            this.$refs.scrollContainer.scrollTop = 0;
                        }
                    },

                    toggleState() {
                        if (this.state === 'min') this.snapTo('half');
                        else if (this.state === 'half') this.snapTo('full');
                        else this.snapTo('half');
                    },

                    startDrag(e) {
                        const touch = e.touches[0];
                        const scroller = this.$refs.scrollContainer;

                        // Si está en 'full' y el usuario desliza la lista bajando (scroll normal)
                        if (this.state === 'full' && scroller && scroller.contains(e.target) && scroller.scrollTop >
                            5) {
                            return;
                        }

                        this.isDragging = true;
                        this.startY = touch.clientY;
                        this.startYWhenDragStarted = this.currentY;
                    },

                    onDrag(e) {
                        if (!this.isDragging) return;
                        const touch = e.touches[0];
                        const deltaY = touch.clientY - this.startY;

                        // Si estamos en 'full' pero desliza hacia arriba (intenta hacer pull-down en la lista)
                        if (this.state === 'full' && deltaY < 0) {
                            const scroller = this.$refs.scrollContainer;
                            if (scroller && scroller.contains(e.target)) {
                                // Dejamos que haga scroll normal
                                this.isDragging = false;
                                return;
                            }
                        }

                        let newY = this.startYWhenDragStarted + deltaY;

                        // Fricción al tirar más arriba de 'full'
                        if (newY < 0) newY = newY * 0.2;

                        // Fricción al tirar más abajo de 'min'
                        if (newY > this.positions['min']) {
                            newY = this.positions['min'] + (newY - this.positions['min']) * 0.2;
                        }

                        this.currentY = newY;

                        // Prevenir pull-to-refresh del navegador solo cuando estamos moviendo el panel
                        if (e.cancelable) e.preventDefault();
                    },

                    endDrag() {
                        if (!this.isDragging) return;
                        this.isDragging = false;

                        const deltaY = this.currentY - this.startYWhenDragStarted;

                        // Si el arrastre fue muy corto, regresamos como estaba
                        if (Math.abs(deltaY) < 30) {
                            this.snapTo(this.state);
                            return;
                        }

                        // Determinar nueva posición según dirección
                        if (deltaY > 0) {
                            // Deslizó hacia ABAJO
                            if (this.state === 'full') this.snapTo('half');
                            else if (this.state === 'half') this.snapTo('min');
                            else this.snapTo('min');
                        } else {
                            // Deslizó hacia ARRIBA
                            if (this.state === 'min') this.snapTo('half');
                            else if (this.state === 'half') this.snapTo('full');
                            else this.snapTo('full');
                        }
                    },

                    // Métodos de eventos existentes
                    handleClickCard(cardEl) {
                        this.snapTo('min');
                        const id = parseInt(cardEl.dataset.denunciaId);
                        const lat = parseFloat(cardEl.dataset.lat);
                        const lng = parseFloat(cardEl.dataset.lng);
                        const titulo = cardEl.dataset.titulo;
                        const descripcion = cardEl.dataset.descripcion;
                        const estado = cardEl.dataset.estado;
                        const category = cardEl.dataset.category;
                        const voteBtn = cardEl.querySelector('button svg');
                        const countSpan = cardEl.querySelector('button span');
                        const has_voted = voteBtn?.classList.contains('text-red-500') || false;
                        const votes_count = parseInt(countSpan?.textContent) || 0;

                        setTimeout(() => {
                            window.dispatchEvent(new CustomEvent('fly-to-map', {
                                detail: {
                                    id,
                                    lat,
                                    lng,
                                    titulo,
                                    descripcion,
                                    estado,
                                    votes_count,
                                    category,
                                    has_voted
                                }
                            }));
                        }, 150);
                    },

                    async vote(id, btnElement) {
                        const icon = btnElement.querySelector('svg');
                        const countSpan = btnElement.querySelector('span');
                        const voteForm = document.getElementById('bottom-sheet-vote-form');

                        btnElement.classList.add('scale-90');
                        setTimeout(() => btnElement.classList.remove('scale-90'), 150);

                        const wasVoted = icon.classList.contains('text-red-500');
                        const prevCount = parseInt(countSpan.textContent) || 0;

                        if (wasVoted) {
                            icon.classList.remove('text-red-500', 'fill-current');
                            icon.classList.add('text-slate-300');
                            countSpan.classList.remove('text-red-500');
                            countSpan.classList.add('text-slate-400');
                        } else {
                            icon.classList.remove('text-slate-300');
                            icon.classList.add('text-red-500', 'fill-current');
                            countSpan.classList.remove('text-slate-400');
                            countSpan.classList.add('text-red-500');
                        }

                        try {
                            const formData = new FormData(voteForm);
                            const url = `/denuncias/${id}/votar`;

                            const response = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': formData.get('_token'),
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            if (response.status === 401) {
                                window.location.href = '/login';
                                return;
                            }

                            if (!response.ok) throw new Error('Error al votar');

                            const result = await response.json();
                            countSpan.textContent = result.votes_count;

                            if (result.voted) {
                                icon.classList.remove('text-slate-300');
                                icon.classList.add('text-red-500', 'fill-current');
                                countSpan.classList.add('text-red-500');
                            } else {
                                icon.classList.remove('text-red-500', 'fill-current');
                                icon.classList.add('text-slate-300');
                                countSpan.classList.remove('text-red-500');
                            }

                            // 🔄 Sync: Notificar al info-point para que actualice
                            window.dispatchEvent(new CustomEvent('vote-updated', {
                                detail: {
                                    report_id: id,
                                    votes_count: result.votes_count,
                                    voted: result.voted
                                }
                            }));
                        } catch (err) {
                            console.error('Error:', err);
                            if (wasVoted) {
                                icon.classList.add('text-red-500', 'fill-current');
                                countSpan.classList.add('text-red-500');
                            } else {
                                icon.classList.remove('text-red-500', 'fill-current');
                                countSpan.classList.remove('text-red-500');
                            }
                            countSpan.textContent = prevCount;
                        }
                    }
                }
            };
        }
    </script>
@endonce
