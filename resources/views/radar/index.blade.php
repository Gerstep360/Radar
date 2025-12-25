<x-app-layout>
    <x-slot name="styles">
        <style>
            /* Reset vital para experiencia App Nativa */
            html, body { height: 100%; margin: 0; overflow: hidden; overscroll-behavior: none; }
            .leaflet-container { width: 100%; height: 100%; background: #f1f5f9; }
            .custom-scrollbar::-webkit-scrollbar { width: 0px; background: transparent; }
        </style>
    </x-slot>

    {{-- CONTENEDOR PRINCIPAL (FULL SCREEN) --}}
    <div class="fixed inset-0 w-full h-full bg-slate-200 overflow-hidden">

        {{-- 1. MAPA (FONDO INTERACTIVO) --}}
        {{-- Ocupa todo el espacio detrás de la UI --}}
        <x-map.radar-map :markers="$denuncias->items()" class="absolute inset-0 w-full h-full z-0" />

        {{-- 2. HUD SUPERIOR (Barra de Estado Flotante) --}}
        <x-radar.live-status 
            title="Radar La Guardia" 
            :count="$denuncias->total()" 
            icon="radar"
            class="absolute top-4 left-4 right-4 z-10 safe-area-top"
        />

        {{-- 3. NOTIFICACIONES EN TIEMPO REAL --}}
        <x-radar.notifications position="top-right" :maxVisible="5" :duration="5000" />

        {{-- 4. UI FLOTANTE INFERIOR --}}
        
        {{-- Bottom Sheet (Lista Deslizable) --}}
        <x-radar.bottom-sheet :denuncias="$denuncias" />

        {{-- Info Point (Tarjeta Detalle - Oculta por defecto) --}}
        <x-radar.info-point />

    </div>

    {{-- 5. MODAL CREAR (Global) --}}
    <x-radar.create-modal :categories="$categories" />

</x-app-layout>