<x-app-layout>
    {{-- Estilos para animaciones suaves --}}
    <x-slot name="styles">
        <style>
            .floating-input:focus ~ label,
            .floating-input:not(:placeholder-shown) ~ label {
                top: -0.5rem;
                left: 0.8rem;
                font-size: 0.75rem;
                background-color: white;
                padding: 0 0.25rem;
                color: #3b82f6; /* Blue-500 */
            }
            /* Ocultar scrollbar en grid de fotos horizontal */
            .no-scrollbar::-webkit-scrollbar { display: none; }
            .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        </style>
    </x-slot>

    <div class="min-h-screen bg-slate-50/50 pb-24 md:pb-12">
        
        {{-- Widget Flotante de Progreso (Desktop: Derecha | Móvil: Barra Superior Fina) --}}
        <div class="fixed top-0 left-0 w-full h-1 bg-slate-200 z-50 md:hidden">
            <div id="mobile-progress-bar" class="h-full bg-blue-600 transition-all duration-500 w-0"></div>
        </div>

        <div class="fixed top-24 right-6 bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/50 p-5 z-40 hidden md:block w-64 transition-all hover:scale-105" id="progress-indicator">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Estado del Reporte</span>
                <span id="progress-percent" class="text-xs font-bold text-blue-600">0%</span>
            </div>
            <div class="space-y-3">
                @foreach(['category' => 'Categoría', 'title' => 'Título', 'description' => 'Descripción', 'location' => 'Ubicación', 'photos' => 'Evidencia'] as $key => $label)
                <div class="flex items-center gap-3 group">
                    <div class="w-5 h-5 rounded-full border-2 border-slate-200 flex items-center justify-center transition-colors duration-300" id="step-{{ $key }}">
                        <div class="w-2 h-2 rounded-full bg-slate-200 transition-colors duration-300 dot"></div>
                    </div>
                    <span class="text-sm font-medium text-slate-500 group-hover:text-slate-800 transition-colors">{{ $label }}</span>
                    @if($key === 'photos')
                        <span class="text-xs text-slate-400 ml-auto">(<span id="photo-progress">0</span>/5)</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <div class="max-w-3xl mx-auto">
            <form method="POST" action="{{ route('denuncias.store') }}" enctype="multipart/form-data" class="relative">
                @csrf

                {{-- SECCIÓN 1: EL MAPA (HÉROE) --}}
                <div class="relative w-full z-10 shadow-lg md:rounded-b-3xl overflow-hidden">
                    <x-map.radar-map 
                        :editable="true" 
                        :markers="$nearbyPins ?? []" 
                        height="h-[400px]"
                    />
                    {{-- Gradiente decorativo --}}
                    <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-slate-50/90 to-transparent pointer-events-none"></div>
                </div>

                {{-- SECCIÓN 2: FORMULARIO FLOTANTE --}}
                <div class="px-4 sm:px-6 relative z-20 -mt-16">
                    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-6 sm:p-8 space-y-8">
                        
                        {{-- Header del Formulario --}}
                        <div class="text-center sm:text-left border-b border-slate-100 pb-6">
                            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Nuevo Reporte</h1>
                            <p class="text-sm text-slate-500 mt-1">Tu colaboración ayuda a mejorar La Guardia.</p>
                        </div>

                        {{-- 1. Categoría (Visual Cards) --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">
                                1. Tipo de Problema <span class="text-red-400">*</span>
                            </label>
                            
                            {{-- Input oculto para compatibilidad con backend y JS --}}
                            <input type="hidden" name="category_id" id="category_id" required>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach($categories as $category)
                                <div class="relative group cursor-pointer" onclick="selectCategory('{{ $category->id }}', this)">
                                    <div class="category-card p-4 rounded-2xl border-2 border-slate-100 bg-slate-50 hover:border-blue-200 hover:bg-blue-50/50 transition-all duration-200 flex flex-col items-center justify-center gap-2 h-full text-center">
                                        <div class="text-2xl mb-1 filter grayscale group-hover:grayscale-0 transition-all">
                                            {{-- Iconos simples basados en ID o Nombre --}}
                                            @if(Str::contains($category->name, 'Alumbrado')) 💡
                                            @elseif(Str::contains($category->name, 'Bache')) 🕳️
                                            @elseif(Str::contains($category->name, 'Basura')) 🗑️
                                            @elseif(Str::contains($category->name, 'Ruido')) 🔊
                                            @else ⚠️ @endif
                                        </div>
                                        <span class="text-xs font-bold text-slate-600 group-hover:text-blue-700 leading-tight">
                                            {{ $category->name }}
                                        </span>
                                    </div>
                                    {{-- Checkmark de seleccionado --}}
                                    <div class="absolute top-2 right-2 w-5 h-5 bg-blue-500 rounded-full text-white items-center justify-center hidden check-icon shadow-sm">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- 2. Detalles (Inputs Flotantes) --}}
                        <div class="space-y-6">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">
                                2. Detalles del Incidente <span class="text-red-400">*</span>
                            </label>
                            
                            <div class="relative">
                                <input type="text" name="title" id="title" required placeholder=" "
                                    class="floating-input peer block w-full rounded-xl border-slate-200 px-4 py-4 text-slate-900 focus:border-blue-500 focus:ring-blue-500/20 shadow-sm transition-all outline-none bg-white">
                                <label for="title" class="absolute left-4 top-4 text-slate-400 text-sm transition-all pointer-events-none peer-placeholder-shown:text-slate-400">
                                    Título breve (Ej: Poste caído)
                                </label>
                            </div>

                            <div class="relative">
                                <textarea name="description" id="description" rows="3" required placeholder=" "
                                    class="floating-input peer block w-full rounded-xl border-slate-200 px-4 py-4 text-slate-900 focus:border-blue-500 focus:ring-blue-500/20 shadow-sm transition-all resize-none outline-none bg-white"></textarea>
                                <label for="description" class="absolute left-4 top-4 text-slate-400 text-sm transition-all pointer-events-none peer-placeholder-shown:text-slate-400">
                                    Describe la situación, referencias, hora...
                                </label>
                            </div>
                        </div>

                        {{-- 3. Evidencia (Modern Grid) --}}
                        <div>
                            <div class="flex justify-between items-end mb-4">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">
                                    3. Evidencia <span class="text-slate-300 font-normal normal-case ml-1">(Opcional pero útil)</span>
                                </label>
                                <span class="text-xs font-bold bg-slate-100 text-slate-500 px-2 py-1 rounded-md" id="photo-counter-badge">0/5</span>
                            </div>

                            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 border-dashed">
                                <div class="flex flex-wrap gap-3" id="photos-container">
                                    
                                    {{-- Botón Cámara --}}
                                    <button type="button" onclick="openCamera()" 
                                        class="w-20 h-20 rounded-xl bg-white border border-slate-200 shadow-sm flex flex-col items-center justify-center text-blue-500 hover:text-blue-600 hover:scale-105 transition-all active:scale-95 group">
                                        <div class="bg-blue-50 p-2 rounded-full mb-1 group-hover:bg-blue-100 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </div>
                                        <span class="text-[9px] font-bold uppercase">Cámara</span>
                                    </button>

                                    {{-- Botón Galería --}}
                                    <label class="w-20 h-20 rounded-xl bg-white border border-slate-200 shadow-sm flex flex-col items-center justify-center text-slate-500 hover:text-slate-700 hover:scale-105 transition-all cursor-pointer active:scale-95 group" for="file-input">
                                        <div class="bg-slate-50 p-2 rounded-full mb-1 group-hover:bg-slate-100 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <span class="text-[9px] font-bold uppercase">Galería</span>
                                        <input type="file" name="fotos[]" multiple accept="image/*" class="hidden" id="file-input" onchange="handleFileSelect(event)">
                                    </label>

                                    {{-- Previews se inyectan aquí --}}
                                    <div id="photo-previews" class="contents"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Action Bar Sticky (Móvil) / Botón Normal (Desktop) --}}
                <div class="fixed bottom-0 left-0 w-full bg-white border-t border-slate-200 p-4 md:static md:bg-transparent md:border-0 md:p-0 md:mt-8 z-50">
                    <div class="max-w-3xl mx-auto">
                        <button type="submit" class="w-full bg-slate-900 text-white font-bold py-4 rounded-xl shadow-lg shadow-slate-900/20 hover:bg-slate-800 hover:shadow-slate-900/30 hover:-translate-y-1 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                            <span>Enviar Reporte</span>
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>

                {{-- Modales (Cámara) --}}
                <div id="camera-modal" class="hidden fixed inset-0 bg-black/90 backdrop-blur-sm z-[100] flex flex-col">
                    <div class="flex justify-between items-center p-4 text-white">
                        <h3 class="font-bold">Cámara</h3>
                        <button type="button" onclick="closeCamera()" class="p-2 rounded-full bg-white/10 hover:bg-white/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="flex-1 relative bg-black flex items-center justify-center overflow-hidden">
                        <video id="camera-preview" class="absolute inset-0 w-full h-full object-cover" autoplay playsinline></video>
                    </div>
                    <div class="p-6 bg-black flex justify-center gap-4">
                         <button type="button" onclick="capturePhoto()" class="w-16 h-16 rounded-full border-4 border-white flex items-center justify-center shadow-lg active:scale-90 transition-transform bg-white/20">
                            <div class="w-12 h-12 bg-white rounded-full"></div>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        let selectedFiles = [];
        let cameraStream = null;

        // Lógica de Selección de Categoría (Visual)
        function selectCategory(id, cardElement) {
            // Actualizar input hidden
            const input = document.getElementById('category_id');
            input.value = id;
            
            // UI Update
            document.querySelectorAll('.category-card').forEach(el => {
                el.classList.remove('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-500/20');
                el.classList.add('border-slate-100', 'bg-slate-50');
            });
            document.querySelectorAll('.check-icon').forEach(el => el.classList.add('hidden'));

            const card = cardElement.querySelector('.category-card');
            card.classList.remove('border-slate-100', 'bg-slate-50');
            card.classList.add('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-500/20');
            cardElement.querySelector('.check-icon').classList.remove('hidden');

            updateProgress();
        }

        // Manejar selección de archivos
        function handleFileSelect(event) {
            const files = Array.from(event.target.files);
            addFilesToSelection(files);
        }

        function addFilesToSelection(files) {
            const maxFiles = 5;
            const currentCount = selectedFiles.length;
            const availableSlots = maxFiles - currentCount;
            
            if (availableSlots <= 0) {
                alert('Máximo 5 fotos permitidas');
                return;
            }

            const filesToAdd = files.slice(0, availableSlots);
            
            filesToAdd.forEach(file => {
                if (file.type.startsWith('image/')) {
                    if (file.size > 5 * 1024 * 1024) {
                        alert(`La foto "${file.name}" es demasiado grande. Máximo 5MB.`);
                        return;
                    }
                    selectedFiles.push(file);
                }
            });
            
            updatePhotoPreviews();
            updateProgress();

            // Limpiar input para permitir re-selección
            document.getElementById('file-input').value = '';
        }

        function updatePhotoPreviews() {
            const container = document.getElementById('photo-previews');
            const counterBadge = document.getElementById('photo-counter-badge');
            
            container.innerHTML = '';
            counterBadge.textContent = `${selectedFiles.length}/5`;

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'w-20 h-20 rounded-xl relative group overflow-hidden shadow-sm border border-slate-200 animate-[fadeIn_0.3s_ease-out]';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                        <button type="button" onclick="removePhoto(${index})" 
                            class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-6 h-6 text-white drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    `;
                    container.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }

        function removePhoto(index) {
            selectedFiles.splice(index, 1);
            updatePhotoPreviews();
            updateProgress();
        }

        // Cámara
        async function openCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'environment' } 
                });
                cameraStream = stream;
                document.getElementById('camera-preview').srcObject = stream;
                document.getElementById('camera-modal').classList.remove('hidden');
            } catch (error) {
                alert('Error al acceder a la cámara: ' + error.message);
            }
        }

        function closeCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
            document.getElementById('camera-modal').classList.add('hidden');
        }

        function capturePhoto() {
            const video = document.getElementById('camera-preview');
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            
            canvas.toBlob(blob => {
                const file = new File([blob], `foto-camara-${Date.now()}.jpg`, { type: 'image/jpeg' });
                addFilesToSelection([file]);
                closeCamera();
            }, 'image/jpeg', 0.8);
        }

        // Validación Submit
        document.querySelector('form').addEventListener('submit', function(e) {
            // Actualizar input de archivos con el array en memoria
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            document.getElementById('file-input').files = dt.files;

            const category = document.getElementById('category_id').value;
            const title = document.getElementById('title').value;
            const desc = document.getElementById('description').value;
            const lat = document.querySelector('input[name="latitude"]')?.value;

            if(!category || !title || !desc || !lat) {
                e.preventDefault();
                alert('Por favor completa todos los campos requeridos y marca la ubicación.');
                return false;
            }
        });

        // Progreso
        function updateProgress() {
            const steps = {
                category: !!document.getElementById('category_id').value,
                title: !!document.getElementById('title').value.trim(),
                description: document.getElementById('description').value.trim().length >= 10,
                location: !!document.querySelector('input[name="latitude"]')?.value,
                photos: selectedFiles.length > 0
            };

            let completedCount = 0;
            const totalSteps = Object.keys(steps).length;

            Object.keys(steps).forEach(step => {
                const element = document.getElementById(`step-${step}`);
                const dot = element.querySelector('.dot');
                const isCompleted = steps[step];
                
                if(isCompleted) completedCount++;

                if (isCompleted) {
                    element.classList.remove('border-slate-200');
                    element.classList.add('border-blue-500', 'bg-blue-500');
                    dot.classList.remove('bg-slate-200');
                    dot.classList.add('bg-white');
                    // Checkmark trick
                    element.innerHTML = `<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>`;
                } else {
                    element.classList.add('border-slate-200');
                    element.classList.remove('border-blue-500', 'bg-blue-500');
                    element.innerHTML = `<div class="w-2 h-2 rounded-full bg-slate-200 dot"></div>`;
                }

                if (step === 'photos') {
                    document.getElementById('photo-progress').textContent = selectedFiles.length;
                }
            });

            // Update Percent & Bar
            const percent = Math.round((completedCount / totalSteps) * 100);
            document.getElementById('progress-percent').textContent = `${percent}%`;
            document.getElementById('mobile-progress-bar').style.width = `${percent}%`;
        }

        // Listeners
        ['title', 'description'].forEach(id => {
            document.getElementById(id).addEventListener('input', updateProgress);
        });

        // Observer para inputs inyectados por el mapa
        const observer = new MutationObserver(function(mutations) {
            const latInput = document.querySelector('input[name="latitude"]');
            if (latInput) {
                latInput.addEventListener('change', updateProgress); // Leaflet suele disparar eventos change
                // Hack para inputs hidden que cambian programáticamente
                const descriptor = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, "value");
                Object.defineProperty(latInput, "value", {
                    set: function(t) {
                        descriptor.set.apply(this, arguments);
                        updateProgress();
                    },
                    get: function() {
                        return descriptor.get.apply(this);
                    }
                });
                observer.disconnect(); // Ya lo encontramos
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });

    </script>
</x-app-layout>