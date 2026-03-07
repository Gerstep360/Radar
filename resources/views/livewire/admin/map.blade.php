<div>
    {{-- Contenedor Principal (Alto completo para inmersión total) --}}
    <div class="relative w-full overflow-hidden" style="height: calc(100vh - 80px); min-height: 600px;">
        
        {{-- Floating Parameters Menu (Glassmorphism) --}}
        <div class="absolute top-6 left-6 z-[1000] bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/40 p-5 w-80 flex flex-col gap-4 transition-all hover:shadow-indigo-500/10">
            <div>
                <h2 class="text-lg font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    Radar Táctico
                </h2>
                <p class="text-[10px] font-bold text-slate-400 mt-2 uppercase tracking-widest pl-12 -mt-2">Monitoreo en Tiempo Real</p>
            </div>
            
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 px-1">Filtrar Categoría</label>
                    <select wire:model.live="category_id" class="w-full pb-0 rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-slate-50 hover:bg-white transition-colors cursor-pointer">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 px-1">Filtrar Estado</label>
                    <div class="relative">
                        <select wire:model.live="status" class="w-full pb-0 rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-slate-50 hover:bg-white transition-colors cursor-pointer appearance-none pl-10">
                            <option value="">Todos los Estados</option>
                            <option value="pendiente">Pendientes</option>
                            <option value="en_revision">En Revisión</option>
                            <option value="atendido">Atendidos</option>
                            <option value="desestimado">Desestimados</option>
                        </select>
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <div class="w-3 h-3 rounded-full 
                                {{ $status === 'pendiente' ? 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.6)]' : '' }}
                                {{ $status === 'en_revision' ? 'bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.6)]' : '' }}
                                {{ $status === 'atendido' ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]' : '' }}
                                {{ $status === 'desestimado' ? 'bg-slate-400' : '' }}
                                {{ empty($status) ? 'bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.6)]' : '' }}
                            "></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contenedor del Mapa Leaflet (Componente Reutilizable protegido de morphDOM) --}}
        <div class="absolute inset-0 z-0 bg-slate-50" wire:ignore>
            <x-map.radar-map 
                :editable="false" 
                height="h-full rounded-none border-0 shadow-none" 
                :markers="$locations" 
            />
        </div>

    </div>
</div>
