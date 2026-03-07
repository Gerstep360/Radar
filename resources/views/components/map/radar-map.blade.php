@props([
    'editable' => false,
    'markers' => [],
    'latName' => 'latitude',
    'lngName' => 'longitude',
    'height' => 'h-80',
    'apiEndpoint' => route('map.points'),
    'canSeeReporter' => auth()->user()?->hasAnyRole(['admin', 'moderador']),
])

@php
    $mapId = 'map-' . uniqid();
    $mapConfig = [
        'mapId' => $mapId,
        'editable' => $editable,
        'markers' => $markers,
        'apiEndpoint' => $apiEndpoint,
    ];
    $functionName = 'radarMap_' . str_replace('-', '_', $mapId);
@endphp

<div x-data="{{ $functionName }}(@js($mapConfig))"
    {{ $attributes->merge(['class' => "relative w-full $height bg-slate-100 rounded-[1.5rem] overflow-hidden shadow-sm border border-slate-200 group touch-none"]) }}
    wire:ignore>

    {{-- SKELETON LOADING --}}
    <div x-ref="skeleton"
        class="absolute inset-0 flex items-center justify-center bg-slate-50 z-20 transition-opacity duration-500">
        <div class="flex flex-col items-center gap-3">
            <div class="w-12 h-12 border-4 border-slate-200 border-t-blue-500 rounded-full animate-spin"></div>
            <span class="text-[10px] font-black text-slate-400 animate-pulse tracking-widest uppercase">Cargando
                Radar...</span>
        </div>
    </div>

    {{-- MAPA --}}
    <div id="{{ $mapId }}" class="w-full h-full z-0 touch-pan-x touch-pan-y" style="min-height: 100%;"></div>

    {{-- UI MODO EDICIÓN --}}
    @if ($editable)
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-[5000]">
            <div class="relative -mt-8 transition-transform duration-200" :class="{ '-translate-y-2': isMoving }">
                <svg class="w-12 h-12 drop-shadow-2xl text-blue-600 transition-all" viewBox="0 0 24 24"
                    fill="currentColor">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke="white"
                        stroke-width="2" />
                    <circle cx="12" cy="9" r="2.5" fill="white" />
                </svg>
                <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-4 h-1 bg-black/20 blur-[2px] rounded-full transition-all duration-200"
                    :class="{ 'scale-75 opacity-50': isMoving }"></div>
            </div>
        </div>

        <input type="hidden" name="{{ $latName }}" x-ref="latInput">
        <input type="hidden" name="{{ $lngName }}" x-ref="lngInput">

        <div class="absolute top-4 left-1/2 -translate-x-1/2 z-[500] pointer-events-none w-full px-4 text-center">
            <div class="bg-white/90 backdrop-blur-md text-slate-600 px-4 py-2 rounded-full shadow-lg border border-slate-100 inline-flex items-center gap-2 transition-all"
                :class="isMoving ? 'scale-95 opacity-80' : 'scale-100 opacity-100'">
                <span class="relative flex h-2.5 w-2.5">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-500"></span>
                </span>
                <span class="text-[10px] font-bold uppercase tracking-wide"
                    x-text="isMoving ? 'Buscando...' : 'Ubicación fijada'"></span>
            </div>
        </div>
    @endif

    {{-- BOTÓN CAMBIO DE CAPA (Satélite / Normal) --}}
    <button @click="toggleLayer()"
        class="absolute bottom-40 right-4 z-[400] h-12 w-12 bg-white text-slate-700 flex items-center justify-center rounded-2xl shadow-xl border border-slate-100 hover:text-blue-600 active:scale-90 transition-all duration-300 group"
        title="Cambiar vista">

        {{-- Icono Satélite (para activar satélite) --}}
        <svg x-show="currentLayer === 'roadmap'" class="w-6 h-6" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
            </path>
        </svg>

        {{-- Icono Mapa (para volver a mapa) --}}
        <svg x-show="currentLayer === 'satellite'" class="w-6 h-6" fill="none" stroke="currentColor"
            viewBox="0 0 24 24" style="display: none;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.806-.984l-4.624-.765m0 13v-8m0 0V2.467">
            </path>
        </svg>
    </button>

    {{-- BOTÓN RECENTRAR --}}
    <button x-show="!isMoving && userLocation" @click="locateUser()" style="display: none;"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-10 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        class="absolute bottom-24 right-4 z-[400] h-12 w-12 bg-white text-slate-700 flex items-center justify-center rounded-2xl shadow-xl border border-slate-100 hover:text-blue-600 active:scale-90 transition-all duration-300">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
            </path>
        </svg>
    </button>
</div>

@once
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        .leaflet-container {
            font-family: inherit;
            z-index: 0;
            background: #f1f5f9;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.8);
                opacity: 0.5;
            }

            100% {
                transform: scale(2.2);
                opacity: 0;
            }
        }

        .priority-pulse::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid currentColor;
            animation: pulse-ring 2s infinite cubic-bezier(0.455, 0.03, 0.515, 0.955);
        }

        .marker-drop-in {
            animation: dropIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            opacity: 0;
            transform-origin: bottom center;
        }

        @keyframes dropIn {
            from {
                transform: translateY(-40px) scale(0);
                opacity: 0;
            }

            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        .leaflet-bottom.leaflet-right {
            display: none !important;
        }

        /* Estilización moderna de popups Leaflet para Radar */
        .custom-radar-popup .leaflet-popup-content-wrapper {
            background: transparent;
            box-shadow: none;
            padding: 0;
            border-radius: 1rem;
        }
        .custom-radar-popup .leaflet-popup-tip-container {
            width: 30px;
            height: 15px;
            margin-left: -15px;
        }
        .custom-radar-popup .leaflet-popup-tip {
            background: white;
            box-shadow: 0 4px 14px rgba(0,0,0,0.1);
        }
    </style>
@endonce

<script>
    function {{ $functionName }}(config) {
        return {
            map: null,
            isMoving: false,
            userLocation: false,
            markersLayer: null,
            currentLayer: 'roadmap', // roadmap | satellite
            layers: {},

            init() {
                // Parche de seguridad global para flyTo (evitar NaN)
                if (window.L && !window.L._flyToPatched) {
                    const originalFlyTo = L.Map.prototype.flyTo;
                    L.Map.prototype.flyTo = function(center, zoom, options) {
                        try {
                            let lat, lng;
                            if (Array.isArray(center)) {
                                [lat, lng] = center;
                            } else if (center && typeof center === 'object') {
                                lat = center.lat ?? center.latitude;
                                lng = center.lng ?? center.longitude;
                            }

                            if (isNaN(parseFloat(lat)) || isNaN(parseFloat(lng))) {
                                console.error('📍 Leaflet flyTo bloqueado: Coordenadas NaN detectadas', { center, zoom });
                                return this;
                            }
                        } catch (e) { console.error('Error en parche flyTo:', e); }
                        return originalFlyTo.apply(this, arguments);
                    };
                    window.L._flyToPatched = true;
                }

                const checkReady = setInterval(() => {
                    if (window.L && document.getElementById(config.mapId)) {
                        clearInterval(checkReady);
                        this.setupMap();
                    }
                }, 50);

                window.addEventListener('fly-to-map', (e) => {
                    const {
                        lat,
                        lng,
                        id,
                        ...data
                    } = e.detail;
                    const fullData = {
                        id,
                        title: data.titulo,
                        description: data.descripcion,
                        ...data
                    };
                    this.selectAndFly(id, lat, lng, fullData);
                });

                window.addEventListener('add-marker-local', (e) => {
                    this.addNewMarker(e.detail);
                });

                // Escucha de evento externo (Ej: Livewire) para reemplazar los marcadores filtrados
                window.addEventListener('map-refresh-data', (e) => {
                    if(this.markersLayer) {
                        this.markersLayer.clearLayers();
                        // En Livewire 3, los datos despachados llegan en e.detail (o e.detail[0])
                        const points = Array.isArray(e.detail) && Array.isArray(e.detail[0]) ? e.detail[0] : (e.detail.points || e.detail);
                        if (Array.isArray(points)) {
                            let bounds = L.latLngBounds();
                            points.forEach((point, index) => {
                                this.createMarker(point, index * 5);
                                if(point.latitude && point.longitude) {
                                    bounds.extend([parseFloat(point.latitude), parseFloat(point.longitude)]);
                                }
                            });
                            // Re-encuadrar el mapa si hay marcadores (Evita que el mapa global se esconda bajo el menú en admin)
                            if(bounds.isValid() && this.map) {
                                this.map.fitBounds(bounds, { paddingBottomRight: [50, 50], paddingTopLeft: [380, 50], maxZoom: 16 });
                            }
                        }
                    }
                });
            },

            setupMap() {
                const mapEl = document.getElementById(config.mapId);
                const defaultLat = -17.8935; // La Guardia
                const defaultLng = -63.3245;

                this.map = L.map(config.mapId, {
                    zoomControl: false,
                    attributionControl: false,
                    zoomSnap: 0.25,
                    fadeAnimation: true
                }).setView([defaultLat, defaultLng], 14.5);

                // --- DEFINICIÓN DE CAPAS GOOGLE ---

                // 1. Google Streets (lyrs=m)
                this.layers.roadmap = L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    attribution: 'Google'
                });

                // 2. Google Hybrid (lyrs=y) - Satélite + Etiquetas
                this.layers.satellite = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    attribution: 'Google'
                });

                // Cargar capa inicial
                this.layers.roadmap.addTo(this.map);

                this.markersLayer = L.layerGroup().addTo(this.map);

                // --- LÓGICA DE DATOS ---
                if (!config.editable && config.markers && config.markers.length > 0) {
                    config.markers.forEach((point, index) => {
                        this.createMarker(point, index * 30);
                    });
                } else if (config.editable) {
                    this.enableEditMode();
                } else {
                    this.fetchPoints();
                }

                const resizeObserver = new ResizeObserver(() => {
                    this.map.invalidateSize();
                });
                resizeObserver.observe(mapEl);

                this.map.on('dragstart', () => {
                    window.dispatchEvent(new CustomEvent('minimize-bottom-sheet'));
                });
                this.map.on('zoomstart', () => {
                    window.dispatchEvent(new CustomEvent('minimize-bottom-sheet'));
                });

                setTimeout(() => {
                    if (this.$refs.skeleton) {
                        this.$refs.skeleton.style.opacity = '0';
                        setTimeout(() => {
                            if (this.$refs.skeleton) this.$refs.skeleton.remove();
                        }, 500);
                    }
                }, 300);

                if (!config.editable) {
                    this.initEcho();
                }

                window.addEventListener(`recenter-map-${config.mapId}`, () => this.locateUser());
                window.addEventListener('map-refresh', () => {
                    if (this.map) {
                        // Intentos múltiples de invalidateSize para asegurar que capte el tamaño real tras transiciones
                        [10, 100, 300, 600].forEach(delay => {
                            setTimeout(() => this.map.invalidateSize(), delay);
                        });
                        // Solo recentrar si no tenemos una ubicación ya fijada o si es la primera carga
                        if (config.editable && !this.userLocation) this.locateUser();
                    }
                });
                window.addEventListener('map-set-view', (e) => {
                    if (this.map && e.detail.lat && e.detail.lng) {
                        this.map.setView([e.detail.lat, e.detail.lng], 16);
                        this.userLocation = true;
                    }
                });
                window.addEventListener('close-info-point', () => {
                    if (this.map) this.map.zoomOut(1);
                });
            },

            // --- FUNCIÓN SWITCH CAPAS ---
            toggleLayer() {
                if (this.currentLayer === 'roadmap') {
                    this.map.removeLayer(this.layers.roadmap);
                    this.layers.satellite.addTo(this.map);
                    this.currentLayer = 'satellite';
                } else {
                    this.map.removeLayer(this.layers.satellite);
                    this.layers.roadmap.addTo(this.map);
                    this.currentLayer = 'roadmap';
                }
            },

            async fetchPoints() {
                try {
                    const response = await fetch(config.apiEndpoint);
                    const result = await response.json();
                    const points = result.data || result;

                    this.markersLayer.clearLayers();
                    points.forEach((point, index) => {
                        this.createMarker(point, index * 30);
                    });
                } catch (error) {
                    console.error('Error fetching points:', error);
                }
            },

            createMarker(point, delay = 0) {
                const lat = parseFloat(point.latitude);
                const lng = parseFloat(point.longitude);
                if (isNaN(lat) || isNaN(lng)) return;

                const votes = point.votes_count || 0;
                const estado = point.estado || point.status || 'pendiente';

                const categoryName = typeof point.category === 'string' ?
                    point.category :
                    (point.category?.name || '');

                let colorClass = 'bg-yellow-500';
                let priorityClass = '';
                let scale = 'scale-100';
                let ringColor = 'border-white';

                switch (estado.toLowerCase()) {
                    case 'atendido':
                        colorClass = 'bg-green-500';
                        break;
                    case 'en_revision':
                        colorClass = 'bg-blue-500';
                        break;
                    case 'desestimado':
                        colorClass = 'bg-slate-400';
                        scale = 'scale-90';
                        break;
                    default:
                        colorClass = 'bg-yellow-500';
                        break;
                }

                const isUrgent = votes > 10 ||
                    categoryName.toLowerCase().includes('seguridad') ||
                    categoryName.toLowerCase().includes('emergencia') ||
                    categoryName.toLowerCase().includes('quema');

                if (isUrgent && estado !== 'atendido' && estado !== 'desestimado') {
                    colorClass = 'bg-red-500 text-red-500';
                    priorityClass = 'priority-pulse z-50';
                    scale = 'scale-125';
                    ringColor = 'border-red-200';
                }

                const iconHtml = `
                    <div class="marker-drop-in w-4 h-4 ${colorClass} rounded-full border-2 ${ringColor} shadow-lg ${priorityClass} transform ${scale} transition-transform hover:scale-150 cursor-pointer"
                         style="animation-delay: ${delay}ms">
                    </div>
                `;

                const icon = L.divIcon({
                    className: 'bg-transparent',
                    html: iconHtml,
                    iconSize: [24, 24],
                    iconAnchor: [12, 12],
                    popupAnchor: [0, -12]
                });

                const marker = L.marker([lat, lng], {
                    icon,
                    id: point.id 
                }).addTo(this.markersLayer);

                // --- CRAFT THE POPUP BUBBLE HTML ---
                // Resolve Image URL safely for storage vs external (like Unsplash)
                let imageUrl = '';
                if (point.image_url) {
                    imageUrl = point.image_url.startsWith('http') ? point.image_url : '/storage/' + point.image_url.replace(/^\/?storage\//, '');
                }

                const username = point.user?.name || 'Ciudadano';
                const canSeeReporter = {{ var_export($canSeeReporter, true) }};

                const popupContent = `
                    <div class="w-64 -m-4 overflow-hidden rounded-2xl bg-white shadow-xl shadow-slate-900/10 border border-slate-100 flex flex-col cursor-pointer hover:shadow-2xl transition-all duration-300" onclick="Livewire.dispatch('open-report-detail', {id: ${point.id}})">
                        ${imageUrl ? `<div class="h-32 w-full bg-slate-100 relative group overflow-hidden">
                            <img src="${imageUrl}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" alt="Evidencia">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <span class="absolute bottom-2 left-2 text-[10px] uppercase tracking-widest font-black text-white bg-${colorClass.replace('bg-','').replace('text-','')} px-2 py-0.5 rounded-full shadow-sm">${estado}</span>
                        </div>` : `<div class="p-3 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                            <span class="text-[10px] uppercase tracking-widest font-black text-slate-500">${categoryName}</span>
                            <span class="w-2.5 h-2.5 rounded-full ${colorClass}"></span>
                        </div>`}
                        <div class="p-4">
                            ${imageUrl ? `<div class="text-[9px] uppercase tracking-wider font-bold text-slate-400 mb-1">${categoryName}</div>` : ''}
                             <h4 class="font-bold text-slate-800 text-sm leading-tight mb-2 truncate">${point.title || 'Reporte de Ciudadano'}</h4>
                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed mb-1">${point.description || 'Sin detalle provisto.'}</p>
                            
                            ${canSeeReporter ? `
                                <div class="flex items-center gap-1.5 mb-3">
                                    <div class="w-4 h-4 rounded-md bg-slate-100 flex items-center justify-center text-[8px] font-black text-slate-500 uppercase">
                                        ${username.charAt(0)}
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400">Por ${username}</span>
                                </div>
                            ` : ''}
                            
                            <div class="flex items-center justify-between mt-auto">
                                <div class="flex flex-col">
                                    <span class="text-[9px] uppercase text-slate-400 font-bold mb-0.5">Apoyos</span>
                                    <div class="flex items-center gap-1 text-slate-700 font-bold text-xs">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"></path></svg>
                                        ${point.votes_count || 0}
                                    </div>
                                </div>
                                <button class="bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm">
                                    Ver Detalle
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                // Configurar el popup personalizado
                const customPopup = L.popup({
                    closeButton: false,
                    className: 'custom-radar-popup',
                    minWidth: 256,
                    maxWidth: 256,
                    offset: [0, 5]
                }).setContent(popupContent);

                // marker.bindPopup(customPopup); // ELIMINADO: Evitar múltiples vistas previas. Usaremos info-point.

                marker.on('click', () => {
                    // 🎯 CONSOLIDACIÓN: Al hacer click, activamos info-point y mod-panel
                    window.dispatchEvent(new CustomEvent('show-info-point', {
                        detail: {
                            id: point.id,
                            lat: lat,
                            lng: lng,
                            titulo: point.title || 'Sin título',
                            descripcion: point.description || 'Sin descripción.',
                            category: categoryName,
                            estado: estado,
                            votes_count: point.votes_count || 0,
                            has_voted: point.has_voted || false,
                            user: point.user
                        }
                    }));

                    @if(auth()->user()->hasAnyRole(['admin', 'moderador']))
                    window.dispatchEvent(new CustomEvent('report-selected-for-mod', {
                        detail: { 
                            id: point.id, 
                            titulo: point.title || 'Sin título', 
                            descripcion: point.description || 'Sin descripción.', 
                            status: estado, 
                            category: categoryName 
                        }
                    }));
                    @endif

                    window.dispatchEvent(new CustomEvent('minimize-bottom-sheet'));
                    
                    // Centrar suavemente
                    const currentZoom = this.map.getZoom();
                    if(currentZoom < 15) {
                        this.map.flyTo([lat, lng], 16, { animate: true, duration: 1 });
                    } else {
                        this.map.panTo([lat, lng], { animate: true });
                    }
                });
            },

            selectAndFly(id, latParam, lngParam, data) {
                const lat = parseFloat(latParam);
                const lng = parseFloat(lngParam);
                
                if (isNaN(lat) || isNaN(lng)) {
                    console.error('selectAndFly: Lat/Lng is NaN', { latParam, lngParam, data });
                    return;
                }
                const categoryName = typeof data.category === 'string' ?
                    data.category :
                    (data.category?.name || 'Reporte');

                window.dispatchEvent(new CustomEvent('show-info-point', {
                    detail: {
                        id: id,
                        lat: lat,
                        lng: lng,
                        titulo: data.titulo || data.title || 'Sin título',
                        descripcion: data.descripcion || data.description || 'Sin descripción.',
                        category: categoryName,
                        estado: data.estado || data.status || 'pendiente',
                        votes_count: data.votes_count || 0,
                        has_voted: data.has_voted || false,
                        user: data.user // 🔒 Para privacidad de reporte
                    }
                }));

                const offsetLat = 0.0020;
                this.map.flyTo([lat - offsetLat, lng], 17, {
                    animate: true,
                    duration: 1.2,
                    easeLinearity: 0.2
                });
            },

            enableEditMode() {
                const updateCenter = () => {
                    this.isMoving = true;
                    setTimeout(() => this.isMoving = false, 300);
                    const c = this.map.getCenter();
                    const lat = c.lat.toFixed(7);
                    const lng = c.lng.toFixed(7);
                    
                    if (this.$refs.latInput) this.$refs.latInput.value = lat;
                    if (this.$refs.lngInput) this.$refs.lngInput.value = lng;

                    // Notificar a quien le interese (ej: el modal de creación)
                    window.dispatchEvent(new CustomEvent(`map-moved-${config.mapId}`, {
                        detail: { lat, lng }
                    }));

                    // Evento genérico para capturadores simples (como el modal)
                    window.dispatchEvent(new CustomEvent('map-moved', {
                        detail: { lat, lng, mapId: config.mapId }
                    }));
                };
                this.map.on('moveend', updateCenter);
                this.map.on('movestart', () => this.isMoving = true);
                
                // Permitir clic para mover el pin (más intuitivo)
                this.map.on('click', (e) => {
                    this.map.flyTo(e.latlng, this.map.getZoom());
                });

                this.locateUser();
            },

            locateUser() {
                if (!navigator.geolocation) return;

                navigator.geolocation.getCurrentPosition(pos => {
                    this.userLocation = true;
                    this.map.flyTo([pos.coords.latitude, pos.coords.longitude], 16);
                }, err => console.log(err));
            },

            initEcho() {
                if (!window.Echo) {
                    setTimeout(() => this.initEcho(), 1000);
                    return;
                }

                window.Echo.channel('radar')
                    .listen('.report.created', (e) => this.addNewMarker(e))
                    .listen('.vote.updated', (e) => this.updateVoteCount(e))
                    .listen('.report.status-changed', (e) => this.updateMarkerStatus(e));
            },

            addNewMarker(data) {
                const categoryName = typeof data.category === 'string' ?
                    data.category :
                    (data.category?.name || 'General');

                const point = {
                    id: data.id,
                    latitude: data.latitude,
                    longitude: data.longitude,
                    title: data.title,
                    description: data.description,
                    status: data.status,
                    category: {
                        name: categoryName
                    },
                    votes_count: data.votes_count || 0,
                    user: data.user
                };
                this.createMarker(point, 0);
                //this.showToast(`📍 Nuevo reporte: ${data.title}`);
            },

            updateVoteCount(data) {
                window.dispatchEvent(new CustomEvent('vote-updated', {
                    detail: {
                        report_id: data.report_id,
                        votes_count: data.votes_count
                    }
                }));
            },

            updateMarkerStatus(data) {
                this.fetchPoints();
                window.dispatchEvent(new CustomEvent('status-updated', {
                    detail: {
                        id: data.id,
                        new_status: data.new_status,
                        old_status: data.old_status
                    }
                }));
            },

            showToast(message) {
                const toast = document.createElement('div');
                toast.className =
                    'fixed top-4 left-1/2 -translate-x-1/2 z-[9999] bg-white px-4 py-2 rounded-full shadow-lg text-sm font-medium text-slate-700 animate-bounce';
                toast.textContent = message;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            }
        }
    }
</script>
