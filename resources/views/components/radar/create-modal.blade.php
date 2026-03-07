@props(['categories'])

{{-- DICCIONARIO DE ÍCONOS (Igual que antes) --}}
@php
    $getIconPath = function($name) {
        $n = Str::lower($name);
        // Emergencias y Salud
        if (Str::contains($n, ['salud', 'médic', 'hospital', 'ambulanc', 'herido'])) return 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z';
        if (Str::contains($n, ['fuego', 'incendio', 'quema', 'humo'])) return 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z';
        if (Str::contains($n, ['gas', 'fuga', 'olor'])) return 'M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z';
        if (Str::contains($n, ['cable', 'luz', 'electric', 'poste', 'alumbrado'])) return 'M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z';
        
        // Infraestructura y Vías
        if (Str::contains($n, ['bache', 'paviment', 'hueco', 'calle', 'asfalto'])) return 'M3 13.5h.008v.008H3V13.5zm3 0h.008v.008H6V13.5zm3 0h.008v.008H9V13.5zm3 0h.008v.008H12V13.5zm3 0h.008v.008H15V13.5zm3 0h.008v.008H18V13.5zm-6-3h.008v.008H12V10.5zm3 0h.008v.008H15V10.5zm-6 0h.008v.008H9V10.5zm3-3h.008v.008H12V7.5z';
        if (Str::contains($n, ['árbol', 'caído', 'maleza', 'monte', 'lote'])) return 'M12 2.25l-.375.375a.75.75 0 01-1.06 0l-.375-.375M12 2.25V21m0-18.75l.375.375a.75.75 0 001.06 0l.375-.375M12 3.75C8.44 3.75 5.23 5.483 3.327 8.16l-.28.39a.75.75 0 00.12.98l.386.31c2.14 1.71 4.97 2.66 7.947 2.66m.5 0c2.977 0 5.807-.95 7.947-2.66l.386-.31a.75.75 0 00.12-.98l-.28-.39C18.77 5.483 15.56 3.75 12 3.75z';
        if (Str::contains($n, ['drenaje', 'alcantarill', 'agua', 'canal', 'inundación'])) return 'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z';
        if (Str::contains($n, ['obstáculo', 'escombro', 'bloqueo'])) return 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636';
        if (Str::contains($n, ['infraestructura', 'muro', 'daño', 'dañada'])) return 'M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-10.5v10.5';
        
        // Seguridad y Convivencia
        if (Str::contains($n, ['seguridad', 'robo', 'asalto', 'delito', 'policia'])) return 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.74c0 3.321 1.343 6.332 3.523 8.512l.142.141a12.02 12.02 0 0016.67 0l.142-.141A11.99 11.99 0 0021 9.742c0-1.314-.212-2.579-.603-3.763A11.959 11.959 0 0112 2.714z';
        if (Str::contains($n, ['animal', 'perro', 'gato', 'maltrato', 'suelto'])) return 'M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.111-.777V4.5l-3.29.822a9 9 0 01-6.14-.76l-.178-.089a9 9 0 00-6.235-.744L3 4.5V3z';
        if (Str::contains($n, ['ruido', 'musica', 'fiesta', 'sonido'])) return 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z';
        if (Str::contains($n, ['comercio', 'vendedor', 'obstrucción', 'puesto'])) return 'M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z';
        
        // Limpieza y Medio Ambiente
        if (Str::contains($n, ['basura', 'limpieza', 'suciedad', 'contenedor'])) return 'M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0';
        if (Str::contains($n, ['plaga', 'insecto', 'vector'])) return 'M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z';
        
        return 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
    };
@endphp
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
                      id="radar-form-el"
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
                                'bg-red-50/50 border-red-100': status === 'error',
                                'bg-slate-50 border-blue-500 ring-2 ring-blue-100': status === 'manual'
                             }">
                            
                            <div class="flex items-center gap-4">
                                {{-- Icono --}}
                                <div class="relative flex-shrink-0">
                                    <div class="h-12 w-12 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-slate-100 transition-colors duration-300"
                                         :class="{
                                            'text-blue-500': status === 'detecting' || status === 'manual', 
                                            'text-green-500': status === 'success' && accuracy <= 100, 
                                            'text-orange-500': status === 'success' && accuracy > 100,
                                            'text-red-500': status === 'error'
                                         }">
                                        
                                        <template x-if="status === 'detecting'">
                                            <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </template>

                                        {{-- Icono Manual --}}
                                        <template x-if="status === 'manual'">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.806-.984l-4.624-.765m0 13v-8m0 0V2.467" /></svg>
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
                                           'text-blue-700': status === 'manual',
                                           'text-slate-800': status === 'detecting'
                                       }"
                                       x-text="message"></p>
                                    
                                    <p class="text-[10px] font-bold uppercase tracking-wider mt-0.5 font-mono" 
                                       :class="accuracy > 100 ? 'text-orange-400' : 'text-slate-400'"
                                       x-text="coordinates"></p>
                                </div>

                                {{-- Botón Reintentar / Manual --}}
                                <div class="flex flex-col gap-1">
                                    <button type="button" x-show="status !== 'detecting'" @click="getLocation()" 
                                        class="flex-shrink-0 h-8 px-2 bg-white border border-slate-200 text-slate-600 text-[9px] font-bold rounded-lg active:scale-95 transition hover:bg-slate-50 flex items-center gap-1 shadow-sm">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        GPS
                                    </button>
                                    <button type="button" x-show="status !== 'detecting'" @click="toggleManual()" 
                                        class="flex-shrink-0 h-8 px-2 border text-[9px] font-bold rounded-lg active:scale-95 transition flex items-center gap-1 shadow-sm"
                                        :class="status === 'manual' ? 'bg-blue-600 border-blue-700 text-white' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.806-.984l-4.624-.765m0 13v-8m0 0V2.467" /></svg>
                                        Mapa
                                    </button>
                                </div>
                            </div>

                            {{-- Mini Mapa para selección manual --}}
                            <div x-show="status === 'manual'" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 max-h-0"
                                 x-transition:enter-end="opacity-100 max-h-[300px]"
                                 class="mt-4 border-2 border-blue-100 rounded-2xl overflow-hidden shadow-inner">
                                <x-map.radar-map 
                                    :editable="true" 
                                    height="h-52" 
                                    latName="manual_lat" 
                                    lngName="manual_lng" 
                                />
                                <div class="bg-blue-50 p-2 text-[9px] font-bold text-blue-600 text-center uppercase tracking-widest">
                                    Mueve el mapa para fijar el pin azul
                                </div>
                            </div>
                            
                            {{-- Escuchas de eventos para refrescar el mapa al abrir el modal --}}
                            <div @open-radar-modal.window="if (status === 'manual') setTimeout(() => window.dispatchEvent(new CustomEvent('map-refresh')), 400)"></div>

                            <input type="hidden" name="latitude" :value="latitude">
                            <input type="hidden" name="longitude" :value="longitude">
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
                    <div x-data="photoManager()">
                        <input type="file" name="fotos[]" id="file-modal" class="hidden" multiple accept="image/*" @change="handleFiles($event.target.files)">
                        <label for="file-modal" class="group flex flex-col items-center justify-center w-full h-24 bg-white border-2 border-dashed border-slate-300 rounded-3xl cursor-pointer hover:border-blue-500 hover:bg-blue-50/30 transition active:scale-[0.98]">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 group-hover:text-blue-500 group-hover:bg-blue-100 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                </div>
                                <div class="text-left">
                                    <p class="text-xs font-bold text-slate-700 group-hover:text-blue-600 transition">Añadir Evidencia</p>
                                    <p class="text-[10px] text-slate-400 font-medium transition" x-text="selectedFiles.length > 0 ? selectedFiles.length + ' foto(s) lista(s)' : 'Máx 5 fotos (Opcional)'"></p>
                                </div>
                            </div>
                        </label>
                        
                        {{-- Previews --}}
                        <div x-show="previews.length > 0" class="grid grid-cols-5 gap-2 mt-4">
                            <template x-for="(src, index) in previews" :key="index">
                                <div class="relative aspect-square rounded-xl overflow-hidden shadow-sm border border-slate-200 group">
                                    <img :src="src" class="w-full h-full object-cover">
                                    <button type="button" @click="removePhoto(index)" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </form>
            </div>

            {{-- FOOTER --}}
            <div class="absolute bottom-0 left-0 w-full p-5 bg-white/90 backdrop-blur-lg border-t border-slate-100 z-[60]">
                 <button type="button" @click="submitRadarForm($event)" class="w-full bg-slate-900 text-white font-bold text-lg py-4 rounded-2xl shadow-xl shadow-slate-900/20 active:scale-[0.98] hover:bg-black transition-all flex items-center justify-center gap-3">
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
                accuracy: 0,

                init() { 
                    setTimeout(() => this.getLocation(), 500);
                    
                    // Escuchar el movimiento del mapa manual (el ID es dinámico)
                    window.addEventListener('map-moved', (e) => {
                        if (this.status === 'manual') {
                            console.log('Mapa movido detectado en Modal:', e.detail);
                            this.latitude = e.detail.lat;
                            this.longitude = e.detail.lng;
                            this.coordinates = `${parseFloat(this.latitude).toFixed(4)}, ${parseFloat(this.longitude).toFixed(4)}`;
                        }
                    });

                    // Limpiar al enviar reporte exitosamente
                    window.addEventListener('radar-report-sent', () => {
                        this.getLocation();
                    });
                },

                toggleManual() {
                    if (this.status === 'manual') {
                        // Si ya está en manual, no hacemos nada o volvemos a detectar si el usuario quiere
                        return;
                    }
                    
                    this.status = 'manual';
                    this.message = 'Modo Manual activo';
                    this.accuracy = 0;
                    
                    // Asegurar que si tenemos una ubicación previa (aunque sea imprecisa), el mapa manual empiece ahí
                    if (this.latitude && this.longitude) {
                        // El componente mapa escuchará esto si se emite
                        setTimeout(() => {
                            window.dispatchEvent(new CustomEvent('map-set-view', { 
                                detail: { lat: this.latitude, lng: this.longitude } 
                            }));
                        }, 100);
                    }

                    // Forzar refresco del mapa al activar modo manual con varios reintentos
                    [50, 200, 500].forEach(d => {
                        setTimeout(() => window.dispatchEvent(new CustomEvent('map-refresh')), d);
                    });

                    // Intentar obtener centro actual del mapa manual si existe
                    const latInput = document.querySelector('input[name="manual_lat"]');
                    const lngInput = document.querySelector('input[name="manual_lng"]');
                    if (latInput && lngInput && latInput.value) {
                        this.latitude = latInput.value;
                        this.longitude = lngInput.value;
                        this.coordinates = `${parseFloat(this.latitude).toFixed(4)}, ${parseFloat(this.longitude).toFixed(4)}`;
                    }
                },

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
                        timeout: 20000,
                        maximumAge: 0
                    };

                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            this.latitude = pos.coords.latitude.toFixed(7);
                            this.longitude = pos.coords.longitude.toFixed(7);
                            this.accuracy = Math.round(pos.coords.accuracy); 
                            
                            this.coordinates = `${this.latitude}, ${this.longitude}`;
                            
                            if (this.accuracy > 1000) {
                                this.message = `Señal débil (±${(this.accuracy/1000).toFixed(1)}km)`;
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
                    this.coordinates = 'Usa el mapa manualmente';
                    this.accuracy = 99999;
                }
            }
        }

        // Gestor de fotos global para el componente
        let globalPhotos = [];

        function photoManager() {
            return {
                selectedFiles: [],
                previews: [],

                handleFiles(files) {
                    const newFiles = Array.from(files);
                    const availableSlots = 5 - this.selectedFiles.length;
                    
                    if (availableSlots <= 0) {
                        alert('Máximo 5 fotos permitidas');
                        return;
                    }

                    const filesToAdd = newFiles.slice(0, availableSlots);
                    
                    filesToAdd.forEach(file => {
                        if (file.type.startsWith('image/')) {
                            if (file.size > 5 * 1024 * 1024) {
                                alert(`La foto "${file.name}" es demasiado grande. Máximo 5MB.`);
                                return;
                            }
                            this.selectedFiles.push(file);
                            
                            const reader = new FileReader();
                            reader.onload = (e) => this.previews.push(e.target.result);
                            reader.readAsDataURL(file);
                        }
                    });
                    
                    globalPhotos = this.selectedFiles;
                },

                removePhoto(index) {
                    this.selectedFiles.splice(index, 1);
                    this.previews.splice(index, 1);
                    globalPhotos = this.selectedFiles;
                },

                init() {
                    window.addEventListener('radar-report-sent', () => {
                        this.selectedFiles = [];
                        this.previews = [];
                        globalPhotos = [];
                    });
                }
            }
        }

        async function submitRadarForm(event) {
            console.log('Iniciando envío de reporte...');
            const form = document.querySelector('#radar-form-el');
            if (!form) {
                console.error('No se encontró el formulario #radar-form-el');
                return;
            }

            const category = form.querySelector('input[name="category_id"]:checked');
            const titulo = form.querySelector('input[name="title"]');
            const descripcion = form.querySelector('textarea[name="description"]');
            const lat = form.querySelector('input[name="latitude"]');
            const lng = form.querySelector('input[name="longitude"]');

            console.log('Valores del form:', {
                cat: category?.value,
                tit: titulo.value,
                lat: lat.value,
                lng: lng.value
            });

            if (!category) { alert('⚠️ Selecciona una categoría'); return; }
            if (!titulo.value.trim()) { alert('⚠️ Escribe un título'); titulo.focus(); return; }
            if (descripcion.value.trim().length < 10) { 
                alert('⚠️ La descripción debe tener al menos 10 caracteres (llevas ' + descripcion.value.trim().length + ')'); 
                descripcion.focus(); 
                return; 
            }
            if (!lat.value || lat.value === 'null') { 
                alert('🛰️ Esperando señal GPS o usa el mapa.\nSi estás en modo manual, mueve el mapa un poco.'); 
                return; 
            }

            const submitBtn = event.currentTarget;
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><span>Enviando...</span>';
            }

            try {
                const url = form.dataset.route;
                const formData = new FormData(form);
                
                // Limpiar fotos previas en formData si las hay y agregar las de la memoria
                formData.delete('fotos[]');
                globalPhotos.forEach(file => {
                    formData.append('fotos[]', file);
                });

                const response = await window.axios.post(url, formData, { 
                    headers: { 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest' 
                    } 
                });

                if (response.data.success) {
                    console.log('Reporte enviado con éxito:', response.data.report);
                    const report = response.data.report;
                    window.dispatchEvent(new CustomEvent('add-marker-local', { detail: report }));
                    window.dispatchEvent(new CustomEvent('add-card-local', { detail: report }));
                    window.dispatchEvent(new CustomEvent('close-radar-modal'));

                    // Notificación de éxito
                    window.dispatchEvent(new CustomEvent('radar-notification', { 
                        detail: { 
                            type: 'report', 
                            title: '¡Reporte Enviado!', 
                            message: 'Tu denuncia se ha registrado correctamente y ya es visible en el mapa.' 
                        } 
                    }));
                    
                    form.reset();
                    globalPhotos = [];
                    // Forzar limpieza de Alpine si es necesario mediante un evento
                    window.dispatchEvent(new CustomEvent('radar-report-sent'));
                }
            } catch (error) {
                console.error('Error:', error);
                if (error.response?.status === 422) {
                    const errors = error.response.data.errors;
                    alert(`⚠️ ${Object.values(errors)[0][0]}`);
                } else {
                    console.error('Error en la petición:', error.response?.data || error.message);
                    const msg = error.response?.data?.message || 'Error de conexión.';
                    alert(`❌ ${msg}`);
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