<x-app-layout>
    <x-slot name="styles">
        {{-- Leaflet CSS --}}
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
        
        <style>
            /* 1. Asegurar altura del viewport */
            html, body { height: 100%; margin: 0; overflow: hidden; }
            
            /* 2. Contenedor del mapa indestructible */
            #map-container {
                position: absolute;
                inset: 0; /* top:0, right:0, bottom:0, left:0 */
                width: 100vw;
                height: 100vh;
                z-index: 0;
                background-color: #e2e8f0; /* Color de fondo mientras carga */
            }
            
            .hide-scrollbar::-webkit-scrollbar { display: none; }
            .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        </style>
    </x-slot>

    {{-- Layout Full Screen --}}
    <div class="relative h-screen w-full overflow-hidden bg-slate-200" x-data="{ viewMode: 'half' }"> 

        {{-- 1. EL MAPA --}}
<div class="relative h-screen w-full overflow-hidden bg-slate-200">
        {{-- Llamada al componente --}}
        <x-map.radar-map 
            :markers="$denuncias->items()" 
            class="absolute inset-0 w-full h-full z-0" 
        />
        
        {{-- ... resto de tu HUD ... --}}
        <x-radar.bottom-sheet :denuncias="$denuncias" />
    </div>

        {{-- 3. BOTTOM SHEET --}}
        <x-radar.bottom-sheet :denuncias="$denuncias" />

    </div>

    {{-- 4. MODAL CREAR --}}
    <x-radar.create-modal :categories="$categories" />

    {{-- 5. SCRIPTS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            
            const initMap = () => {
                const mapElement = document.getElementById('map-container');
                if (!mapElement) return;

                // Coordenadas
                const defaultLat = -17.8935;
                const defaultLng = -63.3245;

                // Inicializar Leaflet
                const map = L.map('map-container', {
                    zoomControl: false,
                    attributionControl: false,
                    zoomSnap: 0.25
                }).setView([defaultLat, defaultLng], 14.5);

                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    maxZoom: 20
                }).addTo(map);

                // --- SOLUCIÓN MAGIC: ResizeObserver ---
                // Esto vigila si el div cambia de tamaño y arregla el gris automáticamente
                const resizeObserver = new ResizeObserver(() => {
                    map.invalidateSize();
                });
                resizeObserver.observe(mapElement);

                // Marcadores
                const rawMarkers = @json($denuncias->items());
                
                rawMarkers.forEach(m => {
                    const lat = parseFloat(m.latitude);
                    const lng = parseFloat(m.longitude);
                    if (isNaN(lat) || isNaN(lng)) return;

                    const icon = L.divIcon({
                        className: 'bg-transparent',
                        html: `<div class="w-4 h-4 bg-blue-600 rounded-full border-2 border-white shadow-lg transform transition hover:scale-125"></div>`,
                        iconSize: [16, 16],
                        iconAnchor: [8, 8]
                    });

                    L.marker([lat, lng], { icon: icon })
                        .addTo(map)
                        .bindPopup(`<b style="font-family:sans-serif; color:#334155;">${m.titulo}</b>`, {
                            closeButton: false,
                            offset: [0, -10]
                        });
                });

                // Evento para volar al punto
                window.addEventListener('fly-to-map', (e) => {
                    const coords = e.detail;
                    map.flyTo([coords.lat, coords.lng], 18, {
                        animate: true,
                        duration: 1.5
                    });
                });
            };

            // Carga segura de Leaflet
            if (typeof L !== 'undefined') {
                initMap();
            } else {
                const checkL = setInterval(() => {
                    if (typeof L !== 'undefined') {
                        clearInterval(checkL);
                        initMap();
                    }
                }, 50);
            }
        });
    </script>
</x-app-layout>