@props(['denuncias'])

{{-- Form oculto para votos (se reutiliza con diferentes IDs) --}}
<form id="bottom-sheet-vote-form" method="POST" style="display: none;">
    @csrf
</form>

<div x-data="bottomSheet()"
     x-init="$watch('state', value => { 
         window.bottomSheetState = value;
         window.dispatchEvent(new CustomEvent('bottom-sheet-state', { detail: { state: value } }));
     })"
     class="absolute left-0 right-0 z-20 flex flex-col bg-white shadow-[0_-10px_40px_rgba(0,0,0,0.1)] rounded-t-[2rem] transition-all duration-500 cubic-bezier(0.32, 0.72, 0, 1) border-t border-slate-50"
     :class="stateClasses[state]"
     @touchstart="startDrag"
     @touchmove="onDrag"
     @touchend="endDrag"
     style="bottom: 0;">

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
        <button class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1.5 rounded-lg active:scale-95 transition-transform" 
                @click="state = 'full'">
            Ver todos
        </button>
    </div>

    {{-- Lista --}}
    <div class="flex-1 overflow-y-auto px-4 pb-24 space-y-3 bg-slate-50/50 custom-scrollbar" x-ref="scrollContainer">
        @forelse($denuncias as $denuncia)
            <div class="bg-white p-3 rounded-2xl shadow-sm border border-slate-100 flex gap-3 active:scale-[0.98] transition-transform cursor-pointer group hover:border-blue-100 relative"
                 data-denuncia-id="{{ $denuncia->id }}"
                 data-lat="{{ $denuncia->latitude }}"
                 data-lng="{{ $denuncia->longitude }}"
                 data-titulo="{{ $denuncia->title ?? $denuncia->titulo ?? '' }}"
                 data-descripcion="{{ Str::limit($denuncia->description ?? $denuncia->descripcion ?? '', 100) }}"
                 data-estado="{{ $denuncia->status ?? $denuncia->estado ?? 'pendiente' }}"
                 data-category="{{ $denuncia->category?->name ?? 'General' }}"
                 @click="handleClickCard($el)">
                
                {{-- Foto --}}
                <div class="w-16 h-16 bg-slate-100 rounded-xl flex-shrink-0 overflow-hidden relative border border-slate-50">
                    @if($denuncia->media->isNotEmpty() && $denuncia->media->first()->url)
                        <img src="{{ Storage::url($denuncia->media->first()->url) }}" class="w-full h-full object-cover" loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0 flex flex-col justify-center pr-10">
                    <h3 class="font-bold text-slate-800 text-sm truncate">{{ $denuncia->title ?? $denuncia->titulo ?? 'Sin título' }}</h3>
                    <p class="text-[10px] text-slate-500 line-clamp-1 mt-0.5">{{ $denuncia->description ?? $denuncia->descripcion ?? '' }}</p>
                    <div class="flex items-center justify-between mt-1.5">
                        <span class="text-[9px] font-bold text-slate-400 bg-slate-50 px-1.5 py-0.5 rounded">{{ $denuncia->created_at->diffForHumans(null, true) }}</span>
                    </div>
                </div>

                {{-- BOTÓN VOTO DIRECTO --}}
                <button @click.stop="vote({{ $denuncia->id }}, $el)" 
                        class="absolute right-3 bottom-3 p-2 rounded-full hover:bg-slate-50 active:scale-75 transition-all z-10 flex items-center gap-1 group/heart border border-transparent">
                    {{-- Icono Corazón (Rojo si votado, Gris si no) --}}
                    <svg class="w-4 h-4 transition-colors duration-300 {{ $denuncia->has_voted ? 'text-red-500 fill-current' : 'text-slate-300' }}" 
                         fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                    </svg>
                    {{-- Contador --}}
                    <span class="text-xs font-bold transition-colors duration-300 {{ $denuncia->has_voted ? 'text-red-500' : 'text-slate-400' }}">
                        {{ $denuncia->votes_count ?? 0 }}
                    </span>
                </button>

            </div>
        @empty
            <div class="py-10 text-center text-slate-400 text-sm">No hay reportes cercanos</div>
        @endforelse
    </div>
</div>

@once
<script>
    if (typeof window.bottomSheet === 'undefined') {
        window.bottomSheet = function() {
            return {
                state: 'half',
                startY: 0,
                currentY: 0,
                stateClasses: {
                    'min': 'h-[120px] bottom-0',
                    'half': 'h-[45vh] bottom-0',
                    'full': 'h-[92vh] bottom-0'
                },

                init() {
                    // 🎯 Minimizar cuando se interactúa con el mapa (drag, zoom, click marker)
                    window.addEventListener('minimize-bottom-sheet', () => {
                        if (this.state !== 'min') {
                            this.state = 'min';
                        }
                    });

                    // Cuando se cierra el info-point, volver a half
                    window.addEventListener('close-info-point', () => {
                        if (this.state === 'min') {
                            this.state = 'half';
                        }
                    });

                    // 📡 Actualizar votos en tiempo real
                    window.addEventListener('vote-updated', (e) => {
                        const card = document.querySelector(`[data-denuncia-id="${e.detail.report_id}"]`);
                        if (card) {
                            const countSpan = card.querySelector('button span');
                            if (countSpan) {
                                countSpan.textContent = e.detail.votes_count;
                                // Animación sutil
                                countSpan.classList.add('scale-125');
                                setTimeout(() => countSpan.classList.remove('scale-125'), 200);
                            }
                        }
                    });

                    // 📋 Agregar nueva card localmente (sin WebSocket)
                    window.addEventListener('add-card-local', (e) => {
                        this.addCardToList(e.detail);
                    });
                },

                // Agregar nueva card al inicio de la lista
                addCardToList(report) {
                    const container = this.$refs.scrollContainer;
                    if (!container) return;

                    const card = document.createElement('div');
                    card.className = 'bg-white p-3 rounded-2xl shadow-sm border border-blue-200 flex gap-3 active:scale-[0.98] transition-transform cursor-pointer group hover:border-blue-100 relative animate-pulse';
                    card.dataset.denunciaId = report.id;
                    card.dataset.lat = report.latitude;
                    card.dataset.lng = report.longitude;
                    card.dataset.titulo = report.title;
                    card.dataset.descripcion = report.description || '';
                    card.dataset.estado = report.status;
                    card.dataset.category = report.category?.name || 'General';

                    card.innerHTML = `
                        <div class="w-16 h-16 bg-blue-50 rounded-xl flex-shrink-0 overflow-hidden relative border border-blue-100 flex items-center justify-center text-blue-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        </div>
                        <div class="flex-1 min-w-0 flex flex-col justify-center pr-10">
                            <h3 class="font-bold text-slate-800 text-sm truncate">${report.title}</h3>
                            <p class="text-[10px] text-slate-500 line-clamp-1 mt-0.5">${report.description || ''}</p>
                            <div class="flex items-center justify-between mt-1.5">
                                <span class="text-[9px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">Nuevo</span>
                            </div>
                        </div>
                        <button class="absolute right-3 bottom-3 p-2 rounded-full hover:bg-slate-50 active:scale-75 transition-all z-10 flex items-center gap-1 group/heart border border-transparent">
                            <svg class="w-4 h-4 transition-colors duration-300 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-xs font-bold transition-colors duration-300 text-slate-400">0</span>
                        </button>
                    `;

                    // Click en card
                    card.addEventListener('click', () => this.handleClickCard(card));
                    
                    // Click en voto
                    const voteBtn = card.querySelector('button');
                    voteBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        this.vote(report.id, voteBtn);
                    });

                    // Insertar al inicio
                    container.insertBefore(card, container.firstChild);
                    
                    // Quitar animación después de 2s
                    setTimeout(() => card.classList.remove('animate-pulse', 'border-blue-200'), 2000);
                    
                    // Actualizar contador
                    const countEl = document.querySelector('.text-xs.text-slate-400.font-bold.uppercase');
                    if (countEl) {
                        const match = countEl.textContent.match(/(\d+)/);
                        if (match) {
                            countEl.textContent = `${parseInt(match[1]) + 1} Reportes`;
                        }
                    }
                },

                toggleState() {
                    this.state = this.state === 'min' ? 'half' : (this.state === 'half' ? 'full' : 'min');
                },

                startDrag(e) {
                    if (this.$refs.scrollContainer.contains(e.target) && this.$refs.scrollContainer.scrollTop > 0) return;
                    this.startY = e.touches[0].clientY;
                },

                onDrag(e) { this.currentY = e.touches[0].clientY; },

                endDrag() {
                    const diff = this.currentY - this.startY;
                    if (Math.abs(diff) < 50) return;
                    if (diff > 0) this.state = this.state === 'full' ? 'half' : 'min';
                    else this.state = this.state === 'min' ? 'half' : 'full';
                    this.startY = 0; this.currentY = 0;
                },

                handleClickCard(cardEl) {
                    // 1. Minimizar bottom-sheet primero
                    this.state = 'min';
                    
                    // 2. Leer datos del DOM
                    const id = parseInt(cardEl.dataset.denunciaId);
                    const lat = parseFloat(cardEl.dataset.lat);
                    const lng = parseFloat(cardEl.dataset.lng);
                    const titulo = cardEl.dataset.titulo;
                    const descripcion = cardEl.dataset.descripcion;
                    const estado = cardEl.dataset.estado;
                    const category = cardEl.dataset.category;
                    
                    // 3. Leer estado ACTUAL del voto desde el DOM
                    const voteBtn = cardEl.querySelector('button svg');
                    const countSpan = cardEl.querySelector('button span');
                    const has_voted = voteBtn?.classList.contains('text-red-500') || false;
                    const votes_count = parseInt(countSpan?.textContent) || 0;
                    
                    // 4. Esperar a que bottom-sheet se minimice, luego abrir info-point
                    setTimeout(() => {
                        window.dispatchEvent(new CustomEvent('fly-to-map', { 
                            detail: { id, lat, lng, titulo, descripcion, estado, votes_count, category, has_voted } 
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
                    
                    // Optimistic UI
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
                    } catch (err) {
                        console.error('Error:', err);
                        // Rollback
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