<div>
<div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    {{-- ===== TARJETAS DE INDICADORES ===== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Pendientes --}}
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 flex items-center justify-between group hover:border-indigo-100 transition-colors">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pendientes</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-black text-slate-800">{{ $pendingReports }}</h3>
                    <span class="text-xs font-bold text-amber-500 bg-amber-50 px-2 py-0.5 rounded-full">Atención</span>
                </div>
            </div>
            <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center group-hover:bg-amber-100 transition-colors">
                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        {{-- En Proceso --}}
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 flex items-center justify-between group hover:border-indigo-100 transition-colors">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">En Proceso</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-black text-slate-800">{{ $inProgressReports }}</h3>
                </div>
            </div>
            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>

        {{-- Resueltos --}}
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 flex items-center justify-between group hover:border-indigo-100 transition-colors">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Resueltos</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-black text-slate-800">{{ $resolvedReports }}</h3>
                    <span class="text-xs font-bold text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded-full">{{ $resolutionRate }}% éxito</span>
                </div>
            </div>
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        {{-- Incidentes Críticos --}}
        <div class="bg-gradient-to-br from-rose-500 to-red-600 rounded-3xl p-5 shadow-lg shadow-rose-200 flex items-center justify-between text-white relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <p class="text-[11px] font-bold text-rose-100 uppercase tracking-wider mb-1">Incidentes Críticos</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-black">{{ $criticalReportsCount }}</h3>
                    <span class="text-xs font-bold bg-white/20 px-2 py-0.5 rounded-full">+5 votos</span>
                </div>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm relative z-10">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
        </div>
    </div>

    {{-- ===== SECCIÓN CENTRAL: GRÁFICOS Y MÉTRICAS AVANZADAS ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pt-2">
        
        {{-- Tendencia de 14 Días --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 lg:col-span-2 relative overflow-hidden">
            <div class="flex justify-between items-start mb-6 relative z-10">
                <div>
                    <h3 class="font-black text-slate-800 text-lg tracking-tight">Actividad Reciente</h3>
                    <p class="text-xs font-bold text-slate-400">Volumen de denuncias (Últimos 14 días)</p>
                </div>
            </div>
            <div class="h-64 relative z-10" wire:ignore>
                <canvas id="evolutionChart"></canvas>
            </div>
            {{-- Decoración de fondo --}}
            <div class="absolute bottom-0 left-0 w-full h-1/2 bg-gradient-to-t from-indigo-50/50 to-transparent z-0"></div>
        </div>

        {{-- Rendimiento Operativo --}}
        <div class="bg-slate-800 rounded-3xl shadow-xl p-6 text-white relative overflow-hidden flex flex-col justify-between">
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none"></div>
            
            <div class="relative z-10">
                <h3 class="font-black text-white text-lg tracking-tight mb-2">Rendimiento Operativo</h3>
                <p class="text-sm text-slate-300 font-medium">Tiempo promedio de resolución de incidentes.</p>
                
                <div class="mt-8 mb-6">
                    <div class="flex items-end gap-2">
                        <span class="text-5xl font-black tracking-tighter">{{ $averageResolutionTime }}</span>
                        <span class="text-indigo-300 font-bold mb-1">Horas</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-white/10">
                    <p class="text-indigo-200 text-[10px] font-bold uppercase tracking-wide mb-2">Barra de progreso del sistema</p>
                    <div class="w-full bg-white/20 rounded-full h-3">
                        <div class="bg-white h-3 rounded-full transition-all" style="width: {{ $resolutionRate }}%"></div>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-indigo-200 text-[10px]">0%</span>
                        <span class="text-white text-[10px] font-bold">{{ $resolutionRate }}% resuelto</span>
                        <span class="text-indigo-200 text-[10px]">100%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== DISTRIBUCIONES (DONA Y BARRAS) ===== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6" wire:ignore>
            <h3 class="font-black text-slate-800 text-lg tracking-tight mb-1">Distribución por Categoría</h3>
            <p class="text-xs font-bold text-slate-400 mb-6">Tipos de reportes más frecuentes</p>
            <div class="h-64">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 flex flex-col items-center justify-center" wire:ignore>
            <div class="w-full text-left">
                <h3 class="font-black text-slate-800 text-lg tracking-tight mb-1">Estado General</h3>
                <p class="text-xs font-bold text-slate-400 mb-6">Proporción de estados actuales</p>
            </div>
            <div class="h-64 w-full flex justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ===== VISTAS RÁPIDAS (REPORTES URGENTES Y TOP USUARIOS) ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pt-2">
        {{-- Denuncias Críticas/Urgentes --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden lg:col-span-2">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h3 class="font-black text-slate-800 tracking-tight flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Muro de Urgencias
                    </h3>
                    <p class="text-xs text-slate-500 mt-1 font-medium">Incidentes pendientes altamente votados</p>
                </div>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($urgentReports as $report)
                <div class="p-4 hover:bg-slate-50/80 transition-colors flex items-center gap-4">
                    <div class="flex-shrink-0 flex flex-col items-center justify-center w-12 h-12 bg-rose-50 rounded-2xl border border-rose-100 text-rose-600">
                        <svg class="w-4 h-4 mb-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                        <span class="text-xs font-black">{{ $report->votes_count }}</span>
                    </div>
                    <div class="flex-grow min-w-0">
                        <p class="text-sm font-bold text-slate-800 truncate">{{ $report->title }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] uppercase font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">{{ $report->category->name }}</span>
                            <span class="text-xs text-slate-400">• Hace {{ $report->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        {{-- Select inline action --}}
                        <select wire:change="updateReportStatus({{ $report->id }}, $event.target.value)" 
                            class="text-xs font-bold rounded-lg border-0 py-1.5 pl-3 pr-8 cursor-pointer
                            @if(in_array($report->status, ['pendiente','pending'])) bg-amber-50 text-amber-700 ring-1 ring-amber-200 
                            @else bg-blue-50 text-blue-700 ring-1 ring-blue-200 @endif"
                        >
                            <option value="pendiente" @selected(in_array($report->status, ['pendiente','pending']))>Pendiente</option>
                            <option value="en_revision" @selected(in_array($report->status, ['en_revision','in_progress','en_proceso']))>En Revisión</option>
                            <option value="atendido">Atendido</option>
                            <option value="desestimado">Desestimado</option>
                        </select>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center">
                    <p class="text-slate-400 font-bold mb-1">Excelente trabajo 🎉</p>
                    <p class="text-sm text-slate-500">No hay ninguna denuncia crítica pendiente en este momento.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Usuarios Destacados --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-black text-slate-800 tracking-tight flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Top Vigilantes
                </h3>
            </div>
            <div class="p-4 space-y-3">
                @foreach($topUsers as $index => $user)
                <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600 flex-shrink-0">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-grow min-w-0">
                        <p class="text-sm font-bold text-slate-800 truncate">{{ $user->name }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ $user->email }}</p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <div class="text-sm font-black text-indigo-600">{{ $user->reports_count }}</div>
                        <div class="text-[9px] font-bold uppercase text-slate-400">Rep.</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>


    {{-- LOGIC DE CHARTS JS --}}
    <script>
    (function() {
        const primaryColor = '#4f46e5';
        let charts = {};
        
        function renderCharts(data) {
            // Configuración común
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#94a3b8';
            Chart.defaults.scale.grid.color = '#f8fafc';

            // 1. Gráfico de Evolución (Línea) con Gradiente
            const ctxEvol = document.getElementById('evolutionChart');
            if (ctxEvol) {
                if (charts.evol) charts.evol.destroy();
                
                // Crear Gradiente debajo de la curva
                let ctx = ctxEvol.getContext('2d');
                let gradientFill = ctx.createLinearGradient(0, 0, 0, 300);
                gradientFill.addColorStop(0, 'rgba(79, 70, 229, 0.4)'); // Indigo intenso
                gradientFill.addColorStop(1, 'rgba(79, 70, 229, 0.0)'); // Transparente abajo

                charts.evol = new Chart(ctxEvol, {
                    type: 'line',
                    data: {
                        labels: data.evolutionLabels,
                        datasets: [{
                            label: 'Denuncias',
                            data: data.evolutionData,
                            borderColor: primaryColor,
                            backgroundColor: gradientFill,
                            borderWidth: 4,
                            tension: 0.5, // Curva super suave
                            fill: true,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: primaryColor,
                            pointBorderWidth: 3,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            pointHoverBackgroundColor: primaryColor,
                            pointHoverBorderColor: '#ffffff',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: { 
                            legend: { display: false },
                            tooltip: { 
                                backgroundColor: 'rgba(15, 23, 42, 0.9)', // Tooltip oscuro
                                titleFont: { size: 14, family: "'Inter', sans-serif" },
                                bodyFont: { size: 13, family: "'Inter', sans-serif" },
                                padding: 12,
                                cornerRadius: 8,
                                displayColors: false
                            } 
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { size: 11, weight: 'bold' }, color: '#94a3b8' } },
                            y: { grid: { color: '#f1f5f9', borderDash: [4, 4] }, border: { display: false }, ticks: { font: { size: 11, weight: 'bold' }, color: '#cbd5e1' }, beginAtZero: true }
                        }
                    }
                });
            }

            // 2. Gráfico de Categorías (Barras) Redondeadas y con Sombra
            const ctxCat = document.getElementById('categoryChart');
            if (ctxCat) {
                if (charts.cat) charts.cat.destroy();
                
                let ctx2 = ctxCat.getContext('2d');
                let barGradient = ctx2.createLinearGradient(0, 0, 0, 400);
                barGradient.addColorStop(0, '#6366f1'); // Indigo 500
                barGradient.addColorStop(1, '#818cf8'); // Indigo 400

                charts.cat = new Chart(ctxCat, {
                    type: 'bar',
                    data: {
                        labels: data.categoryLabels,
                        datasets: [{
                            label: 'Cantidad',
                            data: data.categoryData,
                            backgroundColor: barGradient,
                            borderRadius: 8,
                            borderSkipped: false, // Redondear también la base
                            barPercentage: 0.5,
                            hoverBackgroundColor: '#4f46e5'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                titleColor: '#1e293b',
                                bodyColor: '#475569',
                                titleFont: { size: 13 },
                                bodyFont: { size: 14, weight: 'bold' },
                                borderColor: '#e2e8f0',
                                borderWidth: 1,
                                padding: 12,
                                boxPadding: 6,
                                cornerRadius: 10
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { size: 11, weight: '600' }, color: '#64748b' } },
                            y: { grid: { color: '#f8fafc' }, border: { display: false }, ticks: { font: { size: 11 }, maxTicksLimit: 5 }, beginAtZero: true }
                        }
                    }
                });
            }

            // 3. Gráfico de Estados (Dona Minimalista Premium)
            const ctxStatus = document.getElementById('statusChart');
            if (ctxStatus) {
                if (charts.status) charts.status.destroy();
                charts.status = new Chart(ctxStatus, {
                    type: 'doughnut',
                    data: {
                        labels: data.statusLabels,
                        datasets: [{
                            data: data.statusData,
                            backgroundColor: [
                                '#f59e0b', // Pendiente (Ambar)
                                '#3b82f6', // Revisión (Azul)
                                '#10b981', // Atendido (Esmeralda)
                                '#94a3b8'  // Desestimado (Gris)
                            ],
                            borderWidth: 4,
                            borderColor: '#ffffff', // Borde blanco grueso para separar rebanadas
                            hoverOffset: 8, // Sale la rebanada al hacer hover
                            borderRadius: 4 // Redondeo interno pequeño
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%', // Dona un poco más gruesa
                        plugins: {
                            legend: { 
                                position: 'right', // Leyenda al lado derecho en lugar de abajo
                                labels: { 
                                    usePointStyle: true, 
                                    pointStyle: 'circle',
                                    padding: 20, 
                                    font: { size: 12, family: "'Inter', sans-serif", weight: '600' },
                                    color: '#475569'
                                } 
                            },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                bodyFont: { size: 14, weight: 'bold' },
                                padding: 12,
                                cornerRadius: 8
                            }
                        }
                    }
                });
            }
        }

        function initOrWait() {
            if (typeof Chart === 'undefined') {
                setTimeout(initOrWait, 100); // Reintenta si la CDN tarda en descargar
                return;
            }
            
            // Inicializar gráficos por primera vez desde PHP
            renderCharts({
                statusLabels: {!! $statusChartLabels !!},
                statusData: {!! $statusChartData !!},
                categoryLabels: {!! $categoryChartLabels !!},
                categoryData: {!! $categoryChartData !!},
                evolutionLabels: {!! $evolutionChartLabels !!},
                evolutionData: {!! $evolutionChartData !!}
            });
        }
        
        initOrWait();
    })();
    </script>
</div>
</div>
