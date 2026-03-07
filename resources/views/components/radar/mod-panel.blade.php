{{-- 
    Componente: <x-radar.mod-panel>
    Panel flotante de herramientas para administradores y moderadores.
    Visible solo para roles: admin, moderador.
    Permite cambiar el estado de reportes directamente desde el mapa.
--}}

@if(auth()->user()->hasAnyRole(['admin', 'moderador']))
<div 
    x-data="{
        open: false,
        selectedReport: null,
        updating: false,
        
        init() {
            // Escuchar clicks en tarjetas de reportes para seleccionar el reporte activo
            window.addEventListener('report-selected-for-mod', (e) => {
                this.selectedReport = e.detail;
                this.open = true;
            });
        },
        
        async updateStatus(status) {
            if (!this.selectedReport || this.updating) return;
            this.updating = true;
            
            try {
                const response = await fetch(`/denuncias/${this.selectedReport.id}/estado`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ status })
                });
                
                if (response.ok) {
                    // Actualizar visualmente la tarjeta en el bottom sheet
                    const card = document.querySelector(`[data-denuncia-id='${this.selectedReport.id}']`);
                    if (card) {
                        card.dataset.estado = status;
                        // Emitir evento para que el mapa actualice el marcador
                        window.dispatchEvent(new CustomEvent('report-status-updated', {
                            detail: { id: this.selectedReport.id, status }
                        }));
                    }
                    this.selectedReport.status = status;
                }
            } catch(e) {
                console.error('Error actualizando estado:', e);
            } finally {
                this.updating = false;
            }
        },
        
        getStatusClass(status) {
            const classes = {
                'pendiente': 'bg-yellow-100 text-yellow-700 border-yellow-200',
                'en_revision': 'bg-blue-100 text-blue-700 border-blue-200',
                'atendido': 'bg-green-100 text-green-700 border-green-200',
                'desestimado': 'bg-red-100 text-red-700 border-red-200',
            };
            return classes[status] || 'bg-slate-100 text-slate-600 border-slate-200';
        },
        
        getStatusLabel(status) {
            const labels = {
                'pendiente': 'Pendiente',
                'en_revision': 'En proceso',
                'atendido': 'Resuelto',
                'desestimado': 'Rechazado',
            };
            return labels[status] || status;
        }
    }"
    class="fixed bottom-[72px] left-0 right-0 z-[60] px-4 pointer-events-none"
    x-show="open"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
>
    <div class="bg-white/95 backdrop-blur-xl rounded-2xl shadow-xl border border-slate-100 p-4 pointer-events-auto max-w-md mx-auto">
        
        {{-- Header del panel --}}
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="h-7 w-7 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-black text-slate-800">Panel Moderación</p>
                    <p class="text-[10px] text-slate-400 truncate max-w-[180px]" x-text="selectedReport?.titulo || 'Reporte seleccionado'"></p>
                </div>
            </div>
            <button @click="open = false" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        {{-- Estado actual --}}
        <div class="mb-3">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Estado actual</p>
            <span 
                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border"
                :class="getStatusClass(selectedReport?.status)"
                x-text="getStatusLabel(selectedReport?.status)"
            ></span>
        </div>
        
        {{-- Acciones de cambio de estado --}}
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Cambiar a</p>
            <div class="grid grid-cols-2 gap-2">
                <button 
                    @click="updateStatus('en_revision')"
                    :disabled="updating || selectedReport?.status === 'en_revision'"
                    class="flex items-center gap-2 px-3 py-2.5 rounded-xl border text-xs font-bold transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                    :class="selectedReport?.status === 'en_revision' ? 'border-blue-300 bg-blue-50 text-blue-700' : 'border-slate-200 hover:border-blue-300 hover:bg-blue-50 text-slate-600 hover:text-blue-700'"
                >
                    <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                    En Proceso
                </button>
                
                <button 
                    @click="updateStatus('atendido')"
                    :disabled="updating || selectedReport?.status === 'atendido'"
                    class="flex items-center gap-2 px-3 py-2.5 rounded-xl border text-xs font-bold transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                    :class="selectedReport?.status === 'atendido' ? 'border-green-300 bg-green-50 text-green-700' : 'border-slate-200 hover:border-green-300 hover:bg-green-50 text-slate-600 hover:text-green-700'"
                >
                    <span class="w-2 h-2 rounded-full bg-green-500 shrink-0"></span>
                    Resuelto
                </button>
                
                <button 
                    @click="updateStatus('pendiente')"
                    :disabled="updating || selectedReport?.status === 'pendiente'"
                    class="flex items-center gap-2 px-3 py-2.5 rounded-xl border text-xs font-bold transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                    :class="selectedReport?.status === 'pendiente' ? 'border-yellow-300 bg-yellow-50 text-yellow-700' : 'border-slate-200 hover:border-yellow-300 hover:bg-yellow-50 text-slate-600 hover:text-yellow-700'"
                >
                    <span class="w-2 h-2 rounded-full bg-yellow-500 shrink-0"></span>
                    Pendiente
                </button>
                
                <button 
                    @click="updateStatus('desestimado')"
                    :disabled="updating || selectedReport?.status === 'desestimado'"
                    class="flex items-center gap-2 px-3 py-2.5 rounded-xl border text-xs font-bold transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                    :class="selectedReport?.status === 'desestimado' ? 'border-red-300 bg-red-50 text-red-700' : 'border-slate-200 hover:border-red-300 hover:bg-red-50 text-slate-600 hover:text-red-700'"
                >
                    <span class="w-2 h-2 rounded-full bg-red-500 shrink-0"></span>
                    Rechazado
                </button>
            </div>
        </div>
        
        {{-- Loading overlay --}}
        <div x-show="updating" class="mt-3 flex items-center gap-2 text-xs text-slate-500">
            <svg class="animate-spin w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Actualizando...
        </div>
        
        {{-- Link al panel completo --}}
        <div class="mt-3 pt-3 border-t border-slate-50">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-center gap-2 text-xs font-bold text-indigo-600 hover:text-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Ir al Panel Admin completo
            </a>
        </div>
    </div>
</div>
@endif
