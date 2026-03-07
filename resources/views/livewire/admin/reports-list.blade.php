<div>
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- ===== FILTROS GLOBALES ===== --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/4">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Buscar</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por título, ID o descripción..."
                        class="pl-10 pb-0 w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-slate-50/50" />
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
            <div class="w-full md:w-1/6">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Categoría</label>
                <select wire:model.live="category_id" class="w-full pb-0 rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-slate-50/50">
                    <option value="">Todas</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-1/6">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Estado</label>
                <select wire:model.live="status" class="w-full pb-0 rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-slate-50/50">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="en_revision">En Revisión</option>
                    <option value="atendido">Atendido</option>
                    <option value="desestimado">Desestimado</option>
                </select>
            </div>
            <div class="w-full md:w-1/6">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Desde</label>
                <input type="date" wire:model.live="date_from" class="pb-0 w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-slate-50/50" />
            </div>
            <div class="w-full md:w-1/6">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Hasta</label>
                <input type="date" wire:model.live="date_to" class="pb-0 w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-slate-50/50" />
            </div>
            <div>
                <button wire:click="clearFilters" class="h-10 px-4 flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-colors text-sm font-semibold">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    Limpiar
                </button>
            </div>
        </div>

        {{-- ===== TABLA DE REPORTE MASIVO ===== --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="font-black text-slate-800 tracking-tight flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Directorio de Denuncias
                </h3>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">{{ $reports->total() }} resultados</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-left text-xs font-bold text-slate-500 uppercase tracking-widest">
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Fecha</th>
                            <th class="px-6 py-4">Denuncia</th>
                            <th class="px-6 py-4">Categoría</th>
                            <th class="px-6 py-4 text-center">Apoyos</th>
                            <th class="px-6 py-4">Estado / Gestión</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($reports as $report)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4 text-sm font-mono text-slate-400">#{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                <div class="font-semibold">{{ $report->created_at->format('d M, Y') }}</div>
                                <div class="text-xs text-slate-400">{{ $report->created_at->format('H:i') }} hs</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($report->media->count() > 0)
                                        <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 border border-slate-200">
                                            @php
                                                $path = $report->media->first()->file_path;
                                                $imgUrl = str_starts_with($path, 'http') ? $path : Storage::url($path);
                                            @endphp
                                            <img src="{{ $imgUrl }}" class="w-full h-full object-cover">
                                        </div>
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0 border border-slate-200">
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-bold text-slate-800 truncate max-w-[200px]">{{ $report->title }}</div>
                                        <div class="text-xs text-slate-500 truncate max-w-[200px] hover:text-clip hover:whitespace-normal transition-all">{{ $report->description }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ $report->category->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-600 font-bold border border-rose-100">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                                    {{ $report->votes->count() }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                {{-- Inline Status Setup --}}
                                <div class="relative">
                                    <select wire:change="updateReportStatus({{ $report->id }}, $event.target.value)" 
                                            class="appearance-none pr-8 pl-3 py-1.5 text-xs font-bold rounded-lg border-0 ring-1 ring-inset
                                        @if(in_array($report->status, ['pendiente','pending'])) bg-amber-50 text-amber-700 ring-amber-200 focus:ring-amber-500
                                        @elseif(in_array($report->status, ['en_revision','in_progress','en_proceso'])) bg-blue-50 text-blue-700 ring-blue-200 focus:ring-blue-500
                                        @elseif(in_array($report->status, ['atendido','resolved','resuelto'])) bg-emerald-50 text-emerald-700 ring-emerald-200 focus:ring-emerald-500
                                        @else bg-rose-50 text-rose-700 ring-rose-200 focus:ring-rose-500
                                        @endif
                                        cursor-pointer transition-colors w-full max-w-[140px]"
                                    >
                                        <option value="pendiente" @selected(in_array($report->status, ['pendiente','pending']))>🔴 Pendiente</option>
                                        <option value="en_revision" @selected(in_array($report->status, ['en_revision','in_progress', 'en_proceso']))>🔵 En Revisión</option>
                                        <option value="atendido" @selected(in_array($report->status, ['atendido','resolved','resuelto']))>🟢 Atendido</option>
                                        <option value="desestimado" @selected(in_array($report->status, ['desestimado','rejected','rechazado']))>⚫ Desestimado</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
                                        <svg class="h-4 w-4" @if(in_array($report->status, ['pendiente','pending'])) text-amber-500 @elseif(in_array($report->status, ['atendido','resolved','resuelto'])) text-emerald-500 @elseif(in_array($report->status, ['en_revision','in_progress','en_proceso'])) text-blue-500 @else text-rose-500 @endif" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                    <div wire:loading wire:target="updateReportStatus({{ $report->id }})" class="absolute right-[-24px] top-1.5">
                                        <div class="animate-spin h-4 w-4 border-2 border-indigo-500 border-t-transparent rounded-full"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="$parent.openReportDetails({{ $report->id }})" class="inline-flex pb-0 items-center justify-center p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-colors" title="Ver detalle">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    <p class="font-bold text-slate-600">No hay denuncias que coincidan con los filtros</p>
                                    <p class="text-sm mt-1">Intenta ajustando tu búsqueda o borrando los filtros activos.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($reports->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                    {{ $reports->links(data: ['scrollTo' => false]) }}
                </div>
            @endif
        </div>
        

        {{-- Toast success --}}
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                class="fixed bottom-6 right-6 z-[999] bg-emerald-500 text-white px-5 py-3 rounded-2xl shadow-xl shadow-emerald-200 flex items-center gap-3 font-bold text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

    </div>
</div>
