@props(['categories'])

{{-- DICCIONARIO DE ÍCONOS (Igual que antes) --}}
@php
    $getIconPath = function($name) {
        $n = Str::lower($name);
        if (Str::contains($n, ['bache', 'paviment', 'calle', 'hueco', 'asfalto'])) return 'M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z';
        if (Str::contains($n, ['quema', 'humo', 'fuego', 'incendio', 'forestal'])) return 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z';
        if (Str::contains($n, ['seguridad', 'robo', 'policia', 'delicuencia', 'asalto'])) return 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z';
        if (Str::contains($n, ['alumbrado', 'luz', 'foco', 'poste', 'oscuridad'])) return 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548 5.478a1 1 0 01-.994.909H8.665a1 1 0 01-.994-.909l-.548-5.478z';
        if (Str::contains($n, ['basura', 'limpieza', 'sucio', 'contenedor', 'residuo'])) return 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16';
        if (Str::contains($n, ['lote', 'baldío', 'baldio', 'maleza', 'monte'])) return 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8';
        if (Str::contains($n, ['mantenimiento', 'reparacion', 'obra', 'pintura'])) return 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z';
        if (Str::contains($n, ['drenaje', 'canal', 'agua', 'tubería', 'fuga'])) return 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z';
        if (Str::contains($n, ['animal', 'perro', 'gato', 'suelto'])) return 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z';
        if (Str::contains($n, ['ruido', 'musica', 'fiesta', 'sonido'])) return 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z';
        return 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
    };
@endphp

<div x-data="{ open: false }" 
     x-init="$watch('open', value => {
        if (value) {
            document.body.style.overflow = 'hidden';
            setTimeout(() => window.dispatchEvent(new Event('resize')), 300);
        } else {
            document.body.style.overflow = '';
        }
     })"
     @open-radar-modal.window="open = true"
     @close-radar-modal.window="open = false" 
     class="relative z-[999] font-sans">

    {{-- MODAL --}}
    <div x-show="open" 
         x-cloak
         style="display: none;"
         class="fixed inset-0 z-[100] flex items-end justify-center sm:items-center" 
         aria-labelledby="modal-title" role="dialog" aria-modal="true">

        {{-- Backdrop --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" 
             @click="open = false"></div>

        {{-- Contenedor --}}
        <div x-show="open"
             x-transition:enter="transform transition ease-out duration-300 cubic-bezier(0.16, 1, 0.3, 1)"
             x-transition:enter-start="translate-y-full sm:translate-y-10 sm:opacity-0"
             x-transition:enter-end="translate-y-0 sm:translate-y-0 sm:opacity-100"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-y-0 sm:translate-y-0 sm:opacity-100"
             x-transition:leave-end="translate-y-full sm:translate-y-10 sm:opacity-0"
             class="relative w-full sm:w-[480px] bg-white rounded-t-[2.5rem] sm:rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col h-[92vh] sm:h-auto sm:max-h-[85vh]">

            {{-- HEADER CORREGIDO: Botón "X" a la derecha --}}
            <div class="px-6 pt-6 pb-4 bg-white/80 backdrop-blur-xl border-b border-slate-50 z-50 flex-shrink-0 sticky top-0">
                <div class="w-full flex justify-center mb-4">
                    <div class="h-1.5 w-10 bg-slate-200 rounded-full"></div>
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 tracking-tight leading-none">Reportar</h2>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mt-1">Tu identidad está protegida</p>
                    </div>
                    
                    {{-- BOTÓN CERRAR "X" --}}
                    <button @click="open = false" type="button" 
                        class="h-10 w-10 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-red-500 flex items-center justify-center transition-all active:scale-90 border border-slate-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- BODY --}}
            <div class="flex-1 overflow-y-auto overflow-x-hidden px-6 pt-6 pb-36 space-y-8 custom-scrollbar bg-white">
                <form method="POST" 
                      action="{{ route('denuncias.store') }}" 
                      enctype="multipart/form-data" 
                      id="radar-form"
                      data-route="{{ route('denuncias.store') }}">
                    @csrf

                    {{-- 1. UBICACIÓN GPS CON ALERTA DE PRECISIÓN --}}
                    <div class="space-y-3" x-data="gpsCapture()">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Ubicación Actual</label>
                        
                        {{-- Caja de estado que cambia de color según precisión --}}
                        <div class="relative w-full rounded-3xl overflow-hidden border p-4 transition-all duration-300"
                             :class="{
                                'bg-blue-50/50 border-blue-100': status === 'success' && accuracy <= 100, 
                                'bg-orange-50/50 border-orange-200': status === 'success' && accuracy > 100,
                                'bg-red-50/50 border-red-100': status === 'error'
                             }">
                            
                            <div class="flex items-center gap-4">
                                {{-- Icono --}}
                                <div class="relative flex-shrink-0">
                                    <div class="h-12 w-12 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-slate-100 transition-colors duration-300"
                                         :class="{
                                            'text-blue-500': status === 'detecting', 
                                            'text-green-500': status === 'success' && accuracy <= 100, 
                                            'text-orange-500': status === 'success' && accuracy > 100,
                                            'text-red-500': status === 'error'
                                         }">
                                        
                                        <template x-if="status === 'detecting'">
                                            <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </template>

                                        {{-- Icono OK --}}
                                        <template x-if="status === 'success' && accuracy <= 100">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        </template>

                                        {{-- Icono Warning (Mala precisión) --}}
                                        <template x-if="status === 'success' && accuracy > 100">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                        </template>

                                        {{-- Icono Error --}}
                                        <template x-if="status === 'error'">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </template>
                                    </div>
                                    <div x-show="status === 'detecting'" class="absolute inset-0 rounded-2xl bg-blue-400 animate-ping opacity-20"></div>
                                </div>

                                {{-- Textos --}}
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-sm truncate" 
                                       :class="{
                                           'text-slate-800': status === 'success' && accuracy <= 100,
                                           'text-orange-600': status === 'success' && accuracy > 100,
                                           'text-slate-800': status === 'detecting'
                                       }"
                                       x-text="message"></p>
                                    
                                    <p class="text-[10px] font-bold uppercase tracking-wider mt-0.5 font-mono" 
                                       :class="accuracy > 100 ? 'text-orange-400' : 'text-slate-400'"
                                       x-text="coordinates"></p>
                                </div>

                                {{-- Botón Reintentar (Siempre visible si no está detectando) --}}
                                <button type="button" x-show="status !== 'detecting'" @click="getLocation()" 
                                    class="flex-shrink-0 h-9 px-3 bg-white border border-slate-200 text-slate-600 text-[10px] font-bold rounded-xl active:scale-95 transition hover:bg-slate-50 flex items-center gap-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    Mejorar
                                </button>
                            </div>
                            <input type="hidden" name="latitude" x-model="latitude">
                            <input type="hidden" name="longitude" x-model="longitude">
                        </div>
                    </div>

                    {{-- 2. CATEGORÍAS (DINÁMICAS) --}}
                    <div class="space-y-3">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">¿Qué sucede?</label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach($categories as $category)
                                @php $svgPath = $getIconPath($category->name); @endphp
                                <label class="cursor-pointer group relative">
                                    <input type="radio" name="category_id" value="{{ $category->id }}" class="peer hidden">
                                    <div class="flex flex-col items-center justify-center h-24 rounded-2xl bg-slate-50 border-2 border-transparent peer-checked:border-blue-500 peer-checked:bg-blue-50/50 transition-all duration-200 active:scale-95 hover:bg-slate-100 shadow-sm">
                                        <div class="w-8 h-8 mb-2 text-slate-400 peer-checked:text-blue-600 peer-checked:scale-110 transition-all duration-300">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $svgPath }}"></path>
                                            </svg>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase tracking-wide text-center leading-tight text-slate-400 peer-checked:text-blue-700 px-1">
                                            {{ Str::limit($category->name, 12) }}
                                        </span>
                                    </div>
                                    <div class="absolute top-2 right-2 hidden peer-checked:block text-blue-500 bg-white rounded-full">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- 3. DETALLES --}}
                    <div class="space-y-5">
                        <div class="space-y-1">
                            <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Detalles</label>
                            <input type="text" name="title" placeholder="Ej: Semáforo roto" class="w-full bg-slate-100 border-none rounded-2xl px-5 py-4 text-slate-800 font-bold focus:ring-2 focus:ring-blue-500/50 focus:bg-white transition placeholder:text-slate-400 text-sm shadow-inner">
                        </div>
                        <div>
                            <textarea name="description" rows="3" placeholder="Describe brevemente el problema..." class="w-full bg-slate-100 border-none rounded-2xl px-5 py-4 text-slate-800 text-sm focus:ring-2 focus:ring-blue-500/50 focus:bg-white transition placeholder:text-slate-400 resize-none shadow-inner"></textarea>
                        </div>
                    </div>

                    {{-- 4. FOTOS --}}
                    <div>
                        <input type="file" name="fotos[]" id="file" class="hidden" multiple accept="image/*" onchange="previewFiles(this)" data-optional>
                        <label for="file" class="group flex flex-col items-center justify-center w-full h-24 bg-white border-2 border-dashed border-slate-300 rounded-3xl cursor-pointer hover:border-blue-500 hover:bg-blue-50/30 transition active:scale-[0.98]">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 group-hover:text-blue-500 group-hover:bg-blue-100 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                </div>
                                <div class="text-left">
                                    <p class="text-xs font-bold text-slate-700 group-hover:text-blue-600 transition">Añadir Evidencia</p>
                                    <p class="text-[10px] text-slate-400 font-medium transition" id="file-label">Máx 5 fotos (Opcional)</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </form>
            </div>

            {{-- FOOTER --}}
            <div class="absolute bottom-0 left-0 w-full p-5 bg-white/90 backdrop-blur-lg border-t border-slate-100 z-[60]">
                 <button type="button" onclick="submitRadarForm()" class="w-full bg-slate-900 text-white font-bold text-lg py-4 rounded-2xl shadow-xl shadow-slate-900/20 active:scale-[0.98] hover:bg-black transition-all flex items-center justify-center gap-3">
                    <span>Enviar Reporte</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                </button>
            </div>
        </div>
    </div>
    
    <script>
        function gpsCapture() {
            return {
                status: 'detecting',
                message: 'Buscando satélites...',
                coordinates: '...',
                latitude: null,
                longitude: null,
                accuracy: 0, // Nueva variable para controlar la calidad

                init() { setTimeout(() => this.getLocation(), 500); },

                getLocation() {
                    this.status = 'detecting';
                    this.message = 'Afinando puntería...';
                    this.coordinates = 'Buscando...';
                    
                    if (!navigator.geolocation) {
                        this.handleError('GPS no soportado');
                        return;
                    }

                    const options = {
                        enableHighAccuracy: true,
                        timeout: 20000, // Le damos 20s para que busque bien
                        maximumAge: 0   // Prohibido usar caché
                    };

                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            this.latitude = pos.coords.latitude.toFixed(7);
                            this.longitude = pos.coords.longitude.toFixed(7);
                            this.accuracy = Math.round(pos.coords.accuracy); 
                            
                            this.coordinates = `${this.latitude}, ${this.longitude}`;
                            
                            // Lógica de Semáforo de Calidad
                            if (this.accuracy > 1000) {
                                this.message = `Señal GPS muy débil (±${(this.accuracy/1000).toFixed(1)}km)`;
                            } else if (this.accuracy > 100) {
                                this.message = `Precisión baja (±${this.accuracy}m)`;
                            } else {
                                this.message = `Ubicación Exacta (±${this.accuracy}m)`;
                            }
                            
                            this.status = 'success';
                        },
                        (err) => {
                            console.warn('GPS Error:', err);
                            let msg = 'No se pudo localizar';
                            if (err.code === 1) msg = 'Permiso denegado';
                            if (err.code === 3) msg = 'Sin señal GPS (Timeout)';
                            this.handleError(msg);
                        },
                        options
                    );
                },

                handleError(msg) {
                    this.status = 'error';
                    this.message = msg;
                    this.coordinates = 'Toca "Mejorar" para reintentar';
                    this.accuracy = 99999;
                }
            }
        }

        function previewFiles(input) {
            const label = document.getElementById('file-label');
            if (input.files && input.files.length > 0) {
                label.textContent = `${input.files.length} foto(s) lista(s)`;
                label.className = 'text-[10px] font-bold text-green-600 transition-colors';
            }
        }

        async function submitRadarForm() {
            const forms = document.querySelectorAll('#radar-form');
            let form = null;
            forms.forEach(f => { if (f.getBoundingClientRect().height > 0) form = f; });
            if (!form) return;

            // Validación de Precisión GPS antes de enviar
            // Si quieres ser estricto, descomenta esto:
            /*
            const latInput = form.querySelector('input[name="latitude"]');
            // Hack para leer el scope de Alpine desde fuera (solo si usas window.gpsScope o similar, pero más simple:)
            // Aquí confiamos en que si hay valor, se envía. Pero el usuario ve la alerta visual.
            */

            const category = form.querySelector('input[name="category_id"]:checked');
            const titulo = form.querySelector('input[name="title"]');
            const descripcion = form.querySelector('textarea[name="description"]');
            const lat = form.querySelector('input[name="latitude"]');

            if (!category) { alert('⚠️ Selecciona una categoría'); return; }
            if (!titulo.value.trim()) { alert('⚠️ Escribe un título'); titulo.focus(); return; }
            if (!descripcion.value.trim()) { alert('⚠️ Describe el problema'); descripcion.focus(); return; }
            if (!lat.value) { alert('🛰️ Esperando señal GPS...'); return; }

            const submitBtn = form.closest('.relative').querySelector('button[type="button"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><span>Enviando...</span>';
            }

            try {
                const url = form.dataset.route;
                const formData = new FormData(form);
                const response = await window.axios.post(url, formData, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });

                if (response.data.success) {
                    const report = response.data.report;
                    window.dispatchEvent(new CustomEvent('add-marker-local', { detail: report }));
                    window.dispatchEvent(new CustomEvent('add-card-local', { detail: report }));
                    window.dispatchEvent(new CustomEvent('close-radar-modal'));
                    form.reset();
                    const fileLabel = document.getElementById('file-label');
                    if(fileLabel) { fileLabel.textContent = 'Máx 5 fotos (Opcional)'; fileLabel.className = 'text-[10px] text-slate-400 font-medium'; }
                    
                   /* const toast = document.createElement('div');
                    toast.className = 'fixed top-4 left-1/2 -translate-x-1/2 z-[9999] bg-emerald-500 text-white px-5 py-3 rounded-2xl shadow-2xl shadow-emerald-500/20 text-sm font-bold flex items-center gap-2 animate-bounce';
                    toast.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Reporte enviado`;
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 4000);*/
                }
            } catch (error) {
                console.error('Error:', error);
                if (error.response?.status === 422) {
                    const errors = error.response.data.errors;
                    alert(`⚠️ ${Object.values(errors)[0][0]}`);
                } else {
                    alert('❌ Error de conexión.');
                }
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span>Enviar Reporte</span><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>';
                }
            }
        }
    </script>
</div>