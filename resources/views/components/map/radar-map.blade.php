@props([
    'editable' => false,
    'markers' => [],
    'latName' => 'latitude',
    'lngName' => 'longitude',
    'height' => 'h-80'
])

@php
    $mapId = 'map-' . uniqid();
@endphp

{{-- CONTENEDOR PRINCIPAL --}}
<div x-data="radarMap_{{ str_replace('-', '_', $mapId) }}()"
     {{ $attributes->merge(['class' => "relative w-full $height bg-slate-100 rounded-[1.5rem] overflow-hidden shadow-sm border border-slate-200 group touch-none"]) }}
     wire:ignore>
    
    {{-- 1. SKELETON LOADING --}}
    <div id="skeleton-{{ $mapId }}" class="absolute inset-0 flex items-center justify-center bg-slate-50 z-20 transition-opacity duration-500">
        <div class="flex flex-col items-center gap-3">
            <div class="w-10 h-10 border-4 border-slate-200 border-t-blue-500 rounded-full animate-spin"></div>
            <span class="text-[10px] font-black text-slate-400 animate-pulse tracking-widest uppercase">Cargando...</span>
        </div>
    </div>

    {{-- 2. MAPA LEAFLET --}}
    <div id="{{ $mapId }}" class="w-full h-full z-0 touch-pan-x touch-pan-y" style="min-height: 100%;"></div>

    {{-- 3. MODO EDICIÓN (PIN CENTRAL) --}}
    @if($editable)
        <div class="absolute top-4 left-1/2 -translate-x-1/2 z-[500] pointer-events-none w-full px-4 text-center">
            <div class="bg-white/90 backdrop-blur-md text-slate-600 px-4 py-1.5 rounded-full shadow-lg border border-slate-100 inline-flex items-center gap-2">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
                <span class="text-[10px] font-bold uppercase tracking-wide">Mueve el mapa</span>
            </div>
        </div>
        
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-[400]">
            <div class="relative -mt-8 transition-transform duration-200" :class="{ '-translate-y-2': isMoving }">
                <svg class="w-10 h-10 drop-shadow-xl text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke="white" stroke-width="1.5"/>
                    <circle cx="12" cy="9" r="2.5" fill="white"/>
                </svg>
                <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-4 h-1 bg-black/20 blur-[2px] rounded-full transition-all duration-200"
                     :class="{ 'scale-75 opacity-50': isMoving }"></div>
            </div>
        </div>

        <input type="hidden" name="{{ $latName }}" id="input-lat-{{ $mapId }}">
        <input type="hidden" name="{{ $lngName }}" id="input-lng-{{ $mapId }}">
    @endif

    {{-- 4. PREVIEW CARD FLOTANTE (MINIMALISTA) --}}
    <div x-show="selectedMarker" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full opacity-0"
         class="absolute bottom-6 left-4 right-4 z-[600] bg-white rounded-3xl shadow-2xl border border-slate-100 p-5"
         style="display: none;">
        
        <div class="flex flex-col gap-3">
            <div class="flex justify-between items-start">
                <div class="pr-8">
                    {{-- Título --}}
                    <h3 class="font-black text-slate-800 text-lg leading-tight truncate w-full" x-text="selectedMarker?.title"></h3>
                    
                    {{-- Badge Prioridad (Si aplica) --}}
                    <template x-if="selectedMarker?.priority">
                        <div class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-red-50 text-red-600 rounded-full border border-red-100">
                            <span class="relative flex h-1.5 w-1.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-red-500"></span>
                            </span>
                            <span class="text-[9px] font-black uppercase tracking-wide">Alta Prioridad</span>
                        </div>
                    </template>
                </div>

                {{-- Botón Cerrar --}}
                <button @click="closeCard()" class="absolute top-4 right-4 bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-slate-600 rounded-full p-1.5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Descripción Cortada --}}
            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed" x-text="selectedMarker?.description || 'Sin descripción disponible.'"></p>

            {{-- Botón Acción --}}
            <a :href="'/denuncias/' + selectedMarker?.id" class="w-full bg-slate-900 text-white text-sm font-bold py-3 rounded-2xl text-center shadow-lg shadow-slate-900/10 active:scale-[0.98] transition-transform flex items-center justify-center gap-2 mt-1">
                <span>Ver Completo</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </a>
        </div>
    </div>

    {{-- Botón Recentrar --}}
    <button type="button" id="btn-recenter-{{ $mapId }}" 
        class="absolute bottom-4 right-4 z-[500] bg-white text-slate-700 h-10 w-10 flex items-center justify-center rounded-xl shadow-lg border border-slate-100 hover:text-blue-600 active:scale-90 transition-all duration-200 hidden"
        :class="{'bottom-[140px]': selectedMarker}" 
        onclick="window.dispatchEvent(new CustomEvent('recenter-map-{{ $mapId }}'))">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
    </button>
</div>

@once
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        .leaflet-container { font-family: inherit; z-index: 0; background: #e2e8f0; }
        @keyframes ring-pulse { 0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); } 70% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); } 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); } }
        .priority-marker { animation: ring-pulse 2s infinite; }
    </style>
@endonce

<script>
    function radarMap_{{ str_replace('-', '_', $mapId) }}() {
        return {
            map: null,
            selectedMarker: null,
            isMoving: false,

            init() {
                const mapId = "{{ $mapId }}";
                const isEditable = @json($editable);
                const rawMarkers = @json($markers) || [];
                const defaultLat = -17.8845;
                const defaultLng = -63.3150;

                const parse = (val) => {
                    if (!val) return NaN;
                    if (typeof val === 'string') val = val.replace(',', '.').trim();
                    const num = parseFloat(val);
                    return (!isNaN(num) && isFinite(num) && Math.abs(num) <= 90) ? num : NaN;
                };

                const checkLeaflet = setInterval(() => {
                    if (typeof L !== 'undefined') {
                        clearInterval(checkLeaflet);
                        this.setupMap(mapId, isEditable, rawMarkers, defaultLat, defaultLng, parse);
                    }
                }, 50);
            },

            setupMap(mapId, isEditable, rawMarkers, defaultLat, defaultLng, parse) {
                const mapEl = document.getElementById(mapId);
                if (!mapEl) return;

                this.map = L.map(mapId, {
                    center: [defaultLat, defaultLng],
                    zoom: 15,
                    zoomControl: false,
                    attributionControl: false,
                    zoomSnap: 0.25
                });

                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    maxZoom: 20
                }).addTo(this.map);

                // --- GESTIÓN DE MARCADORES Y SOLAPAMIENTO ---
                if (!isEditable && rawMarkers.length > 0) {
                    // Mapa para detectar duplicados
                    const coordMap = new Map();

                    rawMarkers.forEach(m => {
                        let lat = parse(m.latitude || m.latitud);
                        let lng = parse(m.longitude || m.longitud);
                        if (isNaN(lat) || isNaN(lng)) return;

                        // ALGORITMO DE JITTER (ANT-SOLAPAMIENTO)
                        // Si la coordenada ya existe, le agregamos un pequeño ruido aleatorio
                        const key = `${lat.toFixed(5)},${lng.toFixed(5)}`;
                        if (coordMap.has(key)) {
                            // Desplazamiento aprox de 5-10 metros
                            lat += (Math.random() - 0.5) * 0.00015;
                            lng += (Math.random() - 0.5) * 0.00015;
                        }
                        coordMap.set(key, true);

                        // Lógica de Prioridad
                        const votes = m.votes_count || 0;
                        const isHighPriority = votes > 10 || (m.category && m.category.name.includes('Seguridad'));
                        
                        let colorClass = 'bg-blue-500';
                        if(m.estado === 'resuelto') colorClass = 'bg-green-500';
                        else if(isHighPriority) colorClass = 'bg-red-500';

                        const ringClass = isHighPriority ? 'priority-marker' : '';

                        const icon = L.divIcon({
                            className: 'bg-transparent',
                            html: `<div class="w-3.5 h-3.5 ${colorClass} rounded-full border-2 border-white shadow-lg ${ringClass} transition-transform hover:scale-125"></div>`,
                            iconSize: [14, 14],
                            iconAnchor: [7, 7]
                        });

                        const marker = L.marker([lat, lng], { icon: icon }).addTo(this.map);

                        // CLICK: ZOOM INMERSIVO + CARD
                        marker.on('click', () => {
                            this.selectedMarker = {
                                id: m.id,
                                title: m.title || m.titulo,
                                description: m.description || m.descripcion,
                                priority: isHighPriority
                            };

                            // Desplazar el mapa para que el punto quede visible ARRIBA de la tarjeta
                            // Zoom fuerte (18) + Offset vertical
                            const targetLat = lat - 0.0008; // Offset negativo para subir el mapa (bajar el centro)
                            
                            this.map.flyTo([targetLat, lng], 18, { 
                                duration: 1.2,
                                easeLinearity: 0.1
                            });
                        });
                    });
                }

                if (isEditable) {
                    const updateCenter = () => {
                        this.isMoving = true;
                        setTimeout(() => this.isMoving = false, 300);
                        const c = this.map.getCenter();
                        document.getElementById(`input-lat-${mapId}`).value = c.lat.toFixed(6);
                        document.getElementById(`input-lng-${mapId}`).value = c.lng.toFixed(6);
                        document.getElementById(`status-${mapId}`).innerText = "Ubicación fijada";
                    };
                    this.map.on('moveend', updateCenter);
                    this.map.on('movestart', () => this.isMoving = true);
                    this.locateUser();
                }

                const resizeObserver = new ResizeObserver(() => {
                    this.map.invalidateSize();
                });
                resizeObserver.observe(mapEl);

                setTimeout(() => {
                    document.getElementById(`skeleton-${mapId}`).style.opacity = '0';
                    setTimeout(() => document.getElementById(`skeleton-${mapId}`).remove(), 500);
                }, 500);

                window.addEventListener(`recenter-map-${mapId}`, () => this.locateUser());
            },

            locateUser() {
                if (!navigator.geolocation) return;
                const btn = document.getElementById(`btn-recenter-{{ $mapId }}`);
                if(btn) btn.classList.remove('hidden');

                navigator.geolocation.getCurrentPosition(pos => {
                    this.map.flyTo([pos.coords.latitude, pos.coords.longitude], 16);
                }, err => console.log(err));
            },

            closeCard() {
                this.selectedMarker = null;
                // Alejar un poco al cerrar para dar contexto de nuevo
                this.map.zoomOut(1); 
            }
        }
    }
</script>