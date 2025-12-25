{{-- 
    Componente: <x-radar.notifications>
    
    Sistema de notificaciones en tiempo real para eventos del Radar
    
    Props:
    - position: Posición de las notificaciones: "top-right", "top-left", "bottom-right", "bottom-left" (default: "top-right")
    - maxVisible: Máximo de notificaciones visibles (default: 5)
    - duration: Duración en ms antes de auto-cerrar (default: 5000, 0 = no auto-cerrar)
--}}

@props([
    'position' => 'top-right',
    'maxVisible' => 5,
    'duration' => 5000
])

@php
    $positionClasses = match($position) {
        'top-left' => 'top-20 left-4',
        'top-right' => 'top-20 right-4',
        'bottom-left' => 'bottom-28 left-4',
        'bottom-right' => 'bottom-28 right-4',
        default => 'top-20 right-4'
    };
@endphp

<div 
    x-data="{
        notifications: [],
        maxVisible: {{ $maxVisible }},
        autoDismiss: {{ $duration }},
        idCounter: 0,
        
        get visibleNotifications() {
            return this.notifications.slice(0, this.maxVisible);
        },
        
        init() {
            this.listenToEcho();
            this.listenToLocalEvents();
        },
        
        listenToEcho() {
            if (!window.Echo) return;
            
            window.Echo.channel('radar')
                .listen('.report.created', (e) => {
                    this.addNotification({
                        type: 'report',
                        title: 'Nueva denuncia',
                        message: e.report?.title || 'Se ha creado una nueva denuncia'
                    });
                })
                .listen('.vote.updated', (e) => {
                    this.addNotification({
                        type: 'vote',
                        title: 'Voto recibido',
                        message: 'La denuncia ahora tiene ' + (e.votes_count || 0) + ' votos'
                    });
                })
                .listen('.comment.added', (e) => {
                    this.addNotification({
                        type: 'comment',
                        title: 'Nuevo comentario',
                        message: 'Alguien comentó en una denuncia'
                    });
                })
                .listen('.report.status-changed', (e) => {
                    const labels = { pending: 'Pendiente', in_progress: 'En proceso', resolved: 'Resuelto', rejected: 'Rechazado' };
                    this.addNotification({
                        type: 'status',
                        title: 'Estado actualizado',
                        message: 'La denuncia cambió a: ' + (labels[e.new_status] || e.new_status)
                    });
                });
        },
        
        listenToLocalEvents() {
            window.addEventListener('radar-notification', (e) => this.addNotification(e.detail));
        },
        
        addNotification({ type = 'info', title, message }) {
            const id = ++this.idCounter;
            this.notifications.unshift({ id, type, title, message, time: 'Ahora', visible: true });
            
            if (this.notifications.length > this.maxVisible * 2) {
                this.notifications = this.notifications.slice(0, this.maxVisible * 2);
            }
            
            if (this.autoDismiss > 0) {
                setTimeout(() => this.dismissNotification(id), this.autoDismiss);
            }
        },
        
        dismissNotification(id) {
            const n = this.notifications.find(x => x.id === id);
            if (n) {
                n.visible = false;
                setTimeout(() => {
                    this.notifications = this.notifications.filter(x => x.id !== id);
                }, 300);
            }
        }
    }"
    class="fixed {{ $positionClasses }} z-50 flex flex-col gap-2 pointer-events-none max-w-sm w-full"
    {{ $attributes }}
>
    <template x-for="notification in visibleNotifications" :key="notification.id">
        <div 
            x-show="notification.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-8"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-8"
            class="bg-white/95 backdrop-blur-xl rounded-xl shadow-lg border border-white/50 p-3 pointer-events-auto"
            :class="{
                'border-l-4 border-l-green-500': notification.type === 'report',
                'border-l-4 border-l-blue-500': notification.type === 'vote',
                'border-l-4 border-l-purple-500': notification.type === 'comment',
                'border-l-4 border-l-orange-500': notification.type === 'status',
                'border-l-4 border-l-slate-500': notification.type === 'info'
            }"
        >
            <div class="flex items-start gap-3">
                {{-- Icono según tipo --}}
                <div 
                    class="h-8 w-8 rounded-lg flex items-center justify-center flex-shrink-0"
                    :class="{
                        'bg-green-100 text-green-600': notification.type === 'report',
                        'bg-blue-100 text-blue-600': notification.type === 'vote',
                        'bg-purple-100 text-purple-600': notification.type === 'comment',
                        'bg-orange-100 text-orange-600': notification.type === 'status',
                        'bg-slate-100 text-slate-600': notification.type === 'info'
                    }"
                >
                    {{-- Report Icon --}}
                    <svg x-show="notification.type === 'report'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    
                    {{-- Vote Icon --}}
                    <svg x-show="notification.type === 'vote'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"></path>
                    </svg>
                    
                    {{-- Comment Icon --}}
                    <svg x-show="notification.type === 'comment'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    
                    {{-- Status Icon --}}
                    <svg x-show="notification.type === 'status'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    
                    {{-- Info Icon --}}
                    <svg x-show="notification.type === 'info'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                
                {{-- Contenido --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate" x-text="notification.title"></p>
                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-2" x-text="notification.message"></p>
                    <p class="text-[10px] text-slate-400 mt-1" x-text="notification.time"></p>
                </div>
                
                {{-- Botón cerrar --}}
                <button 
                    @click="dismissNotification(notification.id)"
                    class="text-slate-400 hover:text-slate-600 transition-colors p-1 -m-1"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>
