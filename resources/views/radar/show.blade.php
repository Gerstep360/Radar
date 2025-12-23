{{-- Ejemplo de vista para mostrar una denuncia con sus fotos --}}
<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-4">{{ $denuncia->title }}</h1>
        
        <div class="mb-4">
            <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded text-sm">
                {{ $denuncia->category->name }}
            </span>
            <span class="inline-block bg-gray-100 text-gray-800 px-3 py-1 rounded text-sm ml-2">
                Estado: {{ ucfirst($denuncia->status) }}
            </span>
        </div>

        <p class="text-gray-700 mb-6">{{ $denuncia->description }}</p>

        <div class="mb-6">
            <h3 class="font-semibold mb-2">Ubicación</h3>
            <p class="text-sm text-gray-600">
                Lat: {{ $denuncia->latitude }}, Lng: {{ $denuncia->longitude }}
            </p>
        </div>

        {{-- Galería de fotos --}}
        @if($denuncia->media->count() > 0)
            <div class="mb-6">
                <h3 class="font-semibold mb-3">Evidencia Fotográfica ({{ $denuncia->media->count() }})</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($denuncia->media as $foto)
                        <div class="relative group">
                            <img 
                                src="{{ $foto->url() }}" 
                                alt="Evidencia {{ $loop->iteration }}"
                                class="w-full h-48 object-cover rounded-lg cursor-pointer hover:opacity-90 transition"
                                onclick="openLightbox({{ $loop->index }})"
                            >
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                </svg>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Lightbox simple --}}
            <div id="lightbox" class="hidden fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center" onclick="closeLightbox()">
                <button class="absolute top-4 right-4 text-white text-4xl">&times;</button>
                <button class="absolute left-4 text-white text-4xl" onclick="event.stopPropagation(); prevImage()">&lsaquo;</button>
                <button class="absolute right-4 text-white text-4xl" onclick="event.stopPropagation(); nextImage()">&rsaquo;</button>
                <img id="lightbox-img" class="max-h-full max-w-full" onclick="event.stopPropagation()">
            </div>

            <script>
                const fotos = @json($denuncia->media->map(fn($m) => $m->url()));
                let currentIndex = 0;

                function openLightbox(index) {
                    currentIndex = index;
                    document.getElementById('lightbox-img').src = fotos[currentIndex];
                    document.getElementById('lightbox').classList.remove('hidden');
                }

                function closeLightbox() {
                    document.getElementById('lightbox').classList.add('hidden');
                }

                function nextImage() {
                    currentIndex = (currentIndex + 1) % fotos.length;
                    document.getElementById('lightbox-img').src = fotos[currentIndex];
                }

                function prevImage() {
                    currentIndex = (currentIndex - 1 + fotos.length) % fotos.length;
                    document.getElementById('lightbox-img').src = fotos[currentIndex];
                }

                // Atajos de teclado
                document.addEventListener('keydown', function(e) {
                    if (!document.getElementById('lightbox').classList.contains('hidden')) {
                        if (e.key === 'Escape') closeLightbox();
                        if (e.key === 'ArrowRight') nextImage();
                        if (e.key === 'ArrowLeft') prevImage();
                    }
                });
            </script>
        @else
            <p class="text-gray-500 italic">No hay fotos de evidencia.</p>
        @endif

        <div class="mt-6 text-sm text-gray-500">
            <p>Reportado por: {{ $denuncia->user->name }}</p>
            <p>Fecha: {{ $denuncia->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</div>
