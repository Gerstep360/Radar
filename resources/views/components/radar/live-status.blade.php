{{-- 
    Componente: <x-radar.live-status>
    Barra de estado EN VIVO con búsqueda funcional
--}}

@props([
    'title' => 'Radar La Guardia',
    'count' => 0,
    'icon' => 'radar',
    'sidebar' => false,
])

<div 
    x-data="{
        connectionState: 'connecting',
        reportCount: {{ $count }},
        justUpdated: false,
        checkInterval: null,
        searchOpen: false,
        searchQuery: '',
        
        get isConnected() { return this.connectionState === 'connected'; },
        get isConnecting() { return this.connectionState === 'connecting'; },
        get isDisconnected() { return this.connectionState === 'disconnected'; },
        get statusText() {
            return this.connectionState === 'connected' ? 'En vivo' 
                 : this.connectionState === 'connecting' ? 'Conectando...' 
                 : 'Sin conexión';
        },
        
        init() {
            this.checkEchoConnection();
            this.checkInterval = setInterval(() => this.checkEchoConnection(), 3000);
            window.addEventListener('add-card-local', () => this.incrementCount());
            window.addEventListener('report-created', () => this.incrementCount());
            if (window.Echo) this.subscribeToChannel();
        },
        
        toggleSearch() {
            this.searchOpen = !this.searchOpen;
            if (this.searchOpen) {
                this.$nextTick(() => this.$refs.searchInput?.focus());
            } else {
                this.searchQuery = '';
                window.dispatchEvent(new CustomEvent('filter-reports', { detail: { query: '' } }));
            }
        },
        
        doSearch() {
            window.dispatchEvent(new CustomEvent('filter-reports', { detail: { query: this.searchQuery } }));
        },
        
        checkEchoConnection() {
            if (!window.Echo) { this.connectionState = 'disconnected'; return; }
            const connector = window.Echo.connector;
            if (connector && connector.pusher) {
                const state = connector.pusher.connection.state;
                this.connectionState = state === 'connected' ? 'connected' 
                    : ['connecting', 'initialized', 'enabling'].includes(state) ? 'connecting' 
                    : 'disconnected';
            } else {
                this.connectionState = 'disconnected';
            }
        },
        
        subscribeToChannel() {
            if (!window.Echo) return;
            window.Echo.channel('radar')
                .subscribed(() => { this.connectionState = 'connected'; })
                .error(() => { this.connectionState = 'disconnected'; })
                .listen('.report.created', (e) => {
                    this.incrementCount();
                });
            if (window.Echo.connector?.pusher) {
                window.Echo.connector.pusher.connection.bind('state_change', (s) => {
                    this.connectionState = s.current === 'connected' ? 'connected' 
                        : s.current === 'connecting' ? 'connecting' : 'disconnected';
                });
            }
        },
        
        incrementCount() {
            this.reportCount++;
            this.justUpdated = true;
            setTimeout(() => this.justUpdated = false, 300);
        }
    }"
    {{ $attributes->merge(['class' => $sidebar ? '' : 'pointer-events-none']) }}
>
    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-lg border border-white/50 p-2.5 flex items-center gap-3 pointer-events-auto max-w-md mx-auto transition-all">
        
        {{-- Botón de Búsqueda (FUNCIONAL) --}}
        <button 
            @click="toggleSearch()"
            class="h-10 w-10 rounded-xl flex items-center justify-center shadow-sm border transition-colors shrink-0"
            :class="searchOpen ? 'bg-blue-600 border-blue-500 text-white' : 'bg-slate-100/80 border-white/50 text-slate-500 hover:bg-slate-200'"
            title="Buscar reportes"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </button>

        {{-- Título y Estado (oculto al buscar) --}}
        <div class="flex-1 min-w-0" x-show="!searchOpen" x-transition.opacity>
            <h1 class="text-sm font-black text-slate-800 truncate">{{ $title }}</h1>
            <div class="flex items-center gap-1.5 mt-0.5">
                <template x-if="isConnected">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                </template>
                <template x-if="isConnecting">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-pulse absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                    </span>
                </template>
                <template x-if="isDisconnected">
                    <span class="relative flex h-2 w-2">
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                    </span>
                </template>
                <p 
                    class="text-[10px] font-bold uppercase tracking-wider"
                    :class="{ 'text-green-600': isConnected, 'text-yellow-600': isConnecting, 'text-red-500': isDisconnected }"
                    x-text="statusText"
                ></p>
            </div>
        </div>

        {{-- Campo de búsqueda (visible al buscar) --}}
        <div class="flex-1 min-w-0" x-show="searchOpen" x-transition.opacity>
            <input 
                x-ref="searchInput"
                x-model="searchQuery"
                @input.debounce.300ms="doSearch()"
                @keydown.escape="toggleSearch()"
                type="text" 
                placeholder="Buscar reportes..." 
                class="w-full text-sm font-medium text-slate-700 bg-transparent outline-none placeholder:text-slate-400"
            >
            <p class="text-[10px] text-slate-400 mt-0.5" x-text="searchQuery ? 'Filtrando resultados...' : 'Escribe para buscar'"></p>
        </div>

        {{-- Contador Total --}}
        <div 
            class="h-10 w-10 bg-[#0f172a] rounded-full flex items-center justify-center text-white shadow-md border border-slate-700 transition-transform shrink-0"
            :class="{ 'scale-110': justUpdated }"
        >
            <span class="text-xs font-bold" x-text="reportCount"></span>
        </div>
    </div>
</div>
