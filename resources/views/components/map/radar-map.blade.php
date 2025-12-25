@props([
    'editable' => false,
    'markers' => [],
    'latName' => 'latitude',
    'lngName' => 'longitude',
    'height' => 'h-80',
    'apiEndpoint' => route('map.points')
])

@php
    $mapId = 'map-' . uniqid();
    // Preparamos la configuración en un array PHP limpio
    $mapConfig = [
        'mapId' => $mapId,
        'editable' => $editable,
        'markers' => $markers,
        'apiEndpoint' => $apiEndpoint,
    ];
    $functionName = 'radarMap_' . str_replace('-', '_', $mapId);
@endphp

{{-- 
    SOLUCIÓN CLAVE: 
    Usamos @js($mapConfig) dentro de x-data. 
    Laravel se encarga de escapar las comillas correctamente.
--}}
<div x-data="{{ $functionName }}( @js($mapConfig) )"
     {{ $attributes->merge(['class' => "relative w-full $height bg-slate-100 rounded-[1.5rem] overflow-hidden shadow-sm border border-slate-200 group touch-none"]) }}
     wire:ignore>
    
    {{-- SKELETON LOADING --}}
    <div x-ref="skeleton" class="absolute inset-0 flex items-center justify-center bg-slate-50 z-20 transition-opacity duration-500">
        <div class="flex flex-col items-center gap-3">
            <div class="w-12 h-12 border-4 border-slate-200 border-t-blue-500 rounded-full animate-spin"></div>
            <span class="text-[10px] font-black text-slate-400 animate-pulse tracking-widest uppercase">Cargando Radar...</span>
        </div>
    </div>

    {{-- MAPA --}}
    <div id="{{ $mapId }}" class="w-full h-full z-0 touch-pan-x touch-pan-y" style="min-height: 100%;"></div>

    {{-- UI MODO EDICIÓN --}}
    @if($editable)
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-[400]">
            <div class="relative -mt-8 transition-transform duration-200" :class="{ '-translate-y-2': isMoving }">
                <svg class="w-12 h-12 drop-shadow-2xl text-blue-600 transition-all" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke="white" stroke-width="2"/>
                    <circle cx="12" cy="9" r="2.5" fill="white"/>
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
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-500"></span>
                </span>
                <span class="text-[10px] font-bold uppercase tracking-wide" x-text="isMoving ? 'Buscando...' : 'Ubicación fijada'"></span>
            </div>
        </div>
    @endif

    {{-- BOTÓN RECENTRAR (Protegido con userLocation) --}}
    <button x-show="!isMoving && userLocation" 
            @click="flyToUser()"
            style="display: none;" {{-- Evita parpadeo inicial --}}
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-10 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            class="absolute bottom-24 right-4 z-[400] h-12 w-12 bg-white text-slate-700 flex items-center justify-center rounded-2xl shadow-xl border border-slate-100 hover:text-blue-600 active:scale-90 transition-all duration-300">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
    </button>
</div>

@once
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        .leaflet-container { font-family: inherit; z-index: 0; background: #f1f5f9; }
        @keyframes pulse-ring { 0% { transform: scale(0.8); opacity: 0.5; } 100% { transform: scale(2.2); opacity: 0; } }
        .priority-pulse::after { content: ''; position: absolute; width: 100%; height: 100%; border-radius: 50%; border: 2px solid currentColor; animation: pulse-ring 2s infinite cubic-bezier(0.455, 0.03, 0.515, 0.955); }
        .marker-drop-in { animation: dropIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; opacity: 0; transform-origin: bottom center; }
        @keyframes dropIn { from { transform: translateY(-40px) scale(0); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }
    </style>
@endonce

<script>
    function {{ $functionName }}(config) {
        return {
            map: null,
            isMoving: false, // Inicializado correctamente
            userLocation: null, // Inicializado a null para que x-show funcione
            markersLayer: null,

            init() {
                // Esperar a que el contenedor y Leaflet estén listos
                const checkReady = setInterval(() => {
                    if (window.L && document.getElementById(config.mapId)) {
                        clearInterval(checkReady);
                        this.setupMap();
                    }
                }, 50);

                window.addEventListener('fly-to-map', (e) => {
                    const { lat, lng, id, ...data } = e.detail;
                    const fullData = { id, title: data.titulo, description: data.descripcion, ...data };
                    this.selectAndFly(id, lat, lng, fullData);
                });

                // 🗺️ Agregar marcador local (sin WebSocket)
                window.addEventListener('add-marker-local', (e) => {
                    this.addNewMarker(e.detail);
                });
            },

            setupMap() {
                const mapEl = document.getElementById(config.mapId);
                // La Guardia
                const defaultLat = -17.8935;
                const defaultLng = -63.3245;

                this.map = L.map(config.mapId, {
                    zoomControl: false,
                    attributionControl: false,
                    zoomSnap: 0.25,
                    fadeAnimation: true
                }).setView([defaultLat, defaultLng], 14.5);

                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    maxZoom: 19
                }).addTo(this.map);

                this.markersLayer = L.layerGroup().addTo(this.map);

                // --- CARGA DE DATOS ---
                // Prioridad: Marcadores iniciales (rápido) -> API (actualizado)
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

                // 🎯 Minimizar bottom-sheet al interactuar con el mapa
                this.map.on('dragstart', () => {
                    window.dispatchEvent(new CustomEvent('minimize-bottom-sheet'));
                });
                this.map.on('zoomstart', () => {
                    window.dispatchEvent(new CustomEvent('minimize-bottom-sheet'));
                });

                // Quitar skeleton
                setTimeout(() => {
                    if (this.$refs.skeleton) {
                        this.$refs.skeleton.style.opacity = '0';
                        setTimeout(() => this.$refs.skeleton.remove(), 500);
                    }
                }, 300);

                // 📡 Inicializar WebSocket para tiempo real
                if (!config.editable) {
                    this.initEcho();
                }

                window.addEventListener(`recenter-map-${config.mapId}`, () => this.locateUser());
                window.addEventListener('close-info-point', () => { if (this.map) this.map.zoomOut(1); });
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
                
                // Normalizar category (puede venir como string o como objeto)
                const categoryName = typeof point.category === 'string' 
                    ? point.category 
                    : (point.category?.name || '');
                
                // 🎨 Sistema de colores por ESTADO y PRIORIDAD
                // Estados del sistema: pendiente, en_revision, atendido, desestimado
                let colorClass = 'bg-yellow-500'; // Pendiente por defecto
                let priorityClass = '';
                let scale = 'scale-100';
                let ringColor = 'border-white';

                // 1. Por ESTADO
                switch(estado.toLowerCase()) {
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
                    case 'pendiente':
                    default:
                        colorClass = 'bg-yellow-500';
                        break;
                }

                // 2. URGENTE: Muchos votos o categoría crítica
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
                    iconSize: [16, 16],
                    iconAnchor: [8, 8]
                });

                const marker = L.marker([lat, lng], { icon }).addTo(this.markersLayer);

                marker.on('click', () => {
                    // Minimizar bottom-sheet primero
                    window.dispatchEvent(new CustomEvent('minimize-bottom-sheet'));
                    this.selectAndFly(point.id, lat, lng, point);
                });
            },

            selectAndFly(id, lat, lng, data) {
                // Normalizar category (puede venir como string o como objeto)
                const categoryName = typeof data.category === 'string' 
                    ? data.category 
                    : (data.category?.name || 'Reporte');

                window.dispatchEvent(new CustomEvent('show-info-point', {
                    detail: {
                        id: id,
                        titulo: data.titulo || data.title || 'Sin título',
                        descripcion: data.descripcion || data.description || 'Sin descripción.',
                        category: categoryName,
                        estado: data.estado || data.status || 'pendiente',
                        votes_count: data.votes_count || 0,
                        has_voted: data.has_voted || false
                    }
                }));

                const offsetLat = 0.0020; 
                this.map.flyTo([lat - offsetLat, lng], 17, { animate: true, duration: 1.2, easeLinearity: 0.2 });
            },

            enableEditMode() {
                const updateCenter = () => {
                    this.isMoving = true;
                    setTimeout(() => this.isMoving = false, 300);
                    const c = this.map.getCenter();
                    if (this.$refs.latInput) this.$refs.latInput.value = c.lat.toFixed(7);
                    if (this.$refs.lngInput) this.$refs.lngInput.value = c.lng.toFixed(7);
                };
                this.map.on('moveend', updateCenter);
                this.map.on('movestart', () => this.isMoving = true);
                this.locateUser();
            },

            locateUser() {
                if (!navigator.geolocation) return;
                
                navigator.geolocation.getCurrentPosition(pos => {
                    // Guardamos la ubicación para que Alpine sepa que ya la tenemos (y muestre el botón)
                    this.userLocation = true; 
                    this.map.flyTo([pos.coords.latitude, pos.coords.longitude], 16);
                }, err => console.log(err));
            },

            // 📡 WebSocket: Escuchar eventos en tiempo real
            initEcho() {
                if (!window.Echo) {
                    console.log('Echo not available, retrying...');
                    setTimeout(() => this.initEcho(), 1000);
                    return;
                }

                window.Echo.channel('radar')
                    // Nuevo reporte creado (usa .nombre porque definimos broadcastAs)
                    .listen('.report.created', (e) => {
                        console.log('🆕 Nuevo reporte:', e);
                        this.addNewMarker(e);
                    })
                    // Votos actualizados
                    .listen('.vote.updated', (e) => {
                        console.log('👍 Voto actualizado:', e);
                        this.updateVoteCount(e);
                    })
                    // Estado cambiado (cambio de color)
                    .listen('.report.status-changed', (e) => {
                        console.log('🔄 Estado cambiado:', e);
                        this.updateMarkerStatus(e);
                    });

                console.log('📡 Escuchando canal radar...');
            },

            // Agregar nuevo marcador en tiempo real
            addNewMarker(data) {
                // Normalizar category
                const categoryName = typeof data.category === 'string' 
                    ? data.category 
                    : (data.category?.name || 'General');
                
                const point = {
                    id: data.id,
                    latitude: data.latitude,
                    longitude: data.longitude,
                    title: data.title,
                    description: data.description,
                    status: data.status,
                    category: { name: categoryName },
                    votes_count: data.votes_count || 0,
                    user: data.user
                };
                this.createMarker(point, 0);
                
                // Notificación visual
                this.showToast(`📍 Nuevo reporte: ${data.title}`);
            },

            // Actualizar contador de votos
            updateVoteCount(data) {
                // Emitir evento para que info-point y bottom-sheet actualicen
                window.dispatchEvent(new CustomEvent('vote-updated', {
                    detail: {
                        report_id: data.report_id,
                        votes_count: data.votes_count
                    }
                }));
            },

            // Actualizar estado/color del marcador
            updateMarkerStatus(data) {
                // Recargar todos los marcadores para actualizar colores
                this.fetchPoints();
                
                // Notificar si el punto está siendo visto
                window.dispatchEvent(new CustomEvent('status-updated', {
                    detail: {
                        id: data.id,
                        new_status: data.new_status,
                        old_status: data.old_status
                    }
                }));
            },

            // Toast simple para notificaciones
            showToast(message) {
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 left-1/2 -translate-x-1/2 z-[9999] bg-white px-4 py-2 rounded-full shadow-lg text-sm font-medium text-slate-700 animate-bounce';
                toast.textContent = message;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            }
        }
    }
</script>