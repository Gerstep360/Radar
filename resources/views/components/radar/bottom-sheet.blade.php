@props(['denuncias'])

<div x-data="bottomSheet()"
     class="absolute left-0 right-0 z-30 flex flex-col bg-white shadow-[0_-8px_30px_rgba(0,0,0,0.12)] rounded-t-[2rem] transition-all duration-500 cubic-bezier(0.32, 0.72, 0, 1)"
     :class="stateClasses[state]"
     @touchstart="startDrag"
     @touchmove="onDrag"
     @touchend="endDrag">

    {{-- 1. HANDLE (Barrita para arrastrar) --}}
    <div class="w-full flex justify-center pt-4 pb-2 cursor-pointer touch-none" @click="toggleState()">
        <div class="h-1.5 w-12 bg-slate-200 rounded-full"></div>
    </div>

    {{-- 2. CABECERA DEL SHEET --}}
    <div class="px-6 pb-2">
        <h2 class="text-xl font-black text-slate-800 tracking-tight">Explorar Zona</h2>
        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mt-1">
            {{ $denuncias->count() }} Reportes cercanos
        </p>
    </div>

    {{-- 3. LISTA SCROLLABLE (El contenido) --}}
    <div class="flex-1 overflow-y-auto px-4 pb-24 space-y-3 custom-scrollbar bg-slate-50/50" 
         x-ref="scrollContainer">
        
        @forelse($denuncias as $denuncia)
            <div class="bg-white p-3 rounded-2xl border border-slate-100 shadow-sm flex gap-3 active:scale-[0.98] transition-transform cursor-pointer"
                 onclick="window.dispatchEvent(new CustomEvent('fly-to-map', { detail: { lat: {{ $denuncia->latitude }}, lng: {{ $denuncia->longitude }} } }))">
                
                {{-- Foto Miniatura --}}
                <div class="w-20 h-20 bg-slate-100 rounded-xl flex-shrink-0 overflow-hidden relative">
                    @if($denuncia->media->isNotEmpty() && $denuncia->media->first()->url)
                        <img src="{{ Storage::url($denuncia->media->first()->url) }}" 
                             loading="lazy" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    @endif
                </div>

                {{-- Textos --}}
                <div class="flex-1 min-w-0 flex flex-col justify-between py-1">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm truncate">{{ $denuncia->titulo }}</h3>
                        <p class="text-[11px] text-slate-500 line-clamp-2 leading-snug mt-0.5">{{ $denuncia->descripcion }}</p>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="px-2 py-0.5 bg-slate-100 rounded text-[9px] font-bold text-slate-500">
                            {{ $denuncia->created_at->diffForHumans(null, true) }}
                        </span>
                        <div class="flex items-center gap-1 text-slate-400">
                            <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg>
                            <span class="text-xs font-bold">{{ $denuncia->votes_count ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-10 opacity-50">
                <p class="text-sm font-bold text-slate-400">No hay nada por aquí</p>
            </div>
        @endforelse
    </div>

    <script>
        function bottomSheet() {
            return {
                state: 'half', // min, half, full
                startY: 0,
                currentY: 0,
                stateClasses: {
                    'min': 'h-[15vh] bottom-0',
                    'half': 'h-[45vh] bottom-0',
                    'full': 'h-[95vh] bottom-0'
                },

                toggleState() {
                    if (this.state === 'min') this.state = 'half';
                    else if (this.state === 'half') this.state = 'full';
                    else this.state = 'half'; // De full baja a half
                },

                startDrag(e) {
                    // Solo permitir drag desde el handle o cabecera
                    if (this.$refs.scrollContainer.contains(e.target) && this.$refs.scrollContainer.scrollTop > 0) return;
                    this.startY = e.touches[0].clientY;
                },

                onDrag(e) {
                    // Lógica simplificada: solo detectamos intención, no physics compleja para evitar bugs
                    this.currentY = e.touches[0].clientY;
                },

                endDrag() {
                    const diff = this.currentY - this.startY;
                    if (Math.abs(diff) < 50) return; // Movimiento muy corto ignorar

                    if (diff > 0) { // Deslizar hacia ABAJO
                        if (this.state === 'full') this.state = 'half';
                        else if (this.state === 'half') this.state = 'min';
                    } else { // Deslizar hacia ARRIBA
                        if (this.state === 'min') this.state = 'half';
                        else if (this.state === 'half') this.state = 'full';
                    }
                    this.startY = 0;
                    this.currentY = 0;
                }
            }
        }
    </script>
</div>