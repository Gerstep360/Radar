{{-- 
    Componente: <x-radar.live-status>
    
    Barra de estado EN VIVO con indicador de conexión WebSocket
    
    Props:
    - title: Título a mostrar (default: "Radar La Guardia")
    - count: Número inicial de reportes (default: 0)
    - icon: Icono a usar: "radar", "map", "alert" (default: "radar")
--}}

@props([
    'title' => 'Radar La Guardia',
    'count' => 0,
    'icon' => 'radar'
])

<div 
    x-data="{
        connectionState: 'connecting',
        reportCount: {{ $count }},
        justUpdated: false,
        checkInterval: null,
        
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
                .error(() => { this.connectionState = 'disconnected'; });
                
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
    {{ $attributes->merge(['class' => 'pointer-events-none']) }}
>
    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-lg border border-white/50 p-2.5 flex items-center gap-3 pointer-events-auto max-w-md mx-auto transition-all active:scale-[0.98]">
        
        {{-- Icono --}}
        <div class="h-10 w-10 bg-slate-100/80 rounded-xl flex items-center justify-center text-slate-500 shadow-sm border border-white/50">
            @if($icon === 'radar')
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            @elseif($icon === 'map')
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                </svg>
            @elseif($icon === 'alert')
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            @endif
        </div>

        {{-- Título y Estado de Conexión --}}
        <div class="flex-1 min-w-0">
            <h1 class="text-sm font-black text-slate-800 truncate">{{ $title }}</h1>
            <div class="flex items-center gap-1.5 mt-0.5">
                {{-- Indicador de conexión dinámico --}}
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
                
                {{-- Texto de estado --}}
                <p 
                    class="text-[10px] font-bold uppercase tracking-wider"
                    :class="{
                        'text-green-600': isConnected,
                        'text-yellow-600': isConnecting,
                        'text-red-500': isDisconnected
                    }"
                    x-text="statusText"
                ></p>
            </div>
        </div>

        {{-- Contador Total --}}
        <div 
            class="h-10 w-10 bg-[#0f172a] rounded-full flex items-center justify-center text-white shadow-md shadow-slate-900/20 border border-slate-700 transition-transform"
            :class="{ 'scale-110': justUpdated }"
        >
            <span class="text-xs font-bold" x-text="reportCount"></span>
        </div>
    </div>
</div>
