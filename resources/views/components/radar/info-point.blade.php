@php
    // NO usamos templates, creamos forms reales con @csrf
@endphp

<div id="info-point-container"
     class="fixed left-4 right-4 z-[60] bg-white/95 backdrop-blur-xl rounded-[2rem] shadow-[0_15px_40px_rgba(0,0,0,0.12)] border border-white/50 transform transition-all duration-500 cubic-bezier(0.34, 1.56, 0.64, 1) pointer-events-auto overflow-hidden"
     style="bottom: -300px; visibility: hidden; opacity: 0;">
    
    {{-- Form oculto para votar (se actualiza dinámicamente) --}}
    <form id="vote-form" method="POST" style="display: none;">
        @csrf
    </form>
    
    {{-- Barra de Estado --}}
    <div id="info-status-bar" class="h-1.5 w-full bg-slate-200"></div>

    <div class="p-5">
        {{-- Header --}}
        <div class="flex items-start justify-between mb-3">
            <div class="flex flex-col gap-1">
                <span id="info-category" class="text-[10px] font-black uppercase tracking-widest text-slate-400"></span>
                <span id="info-status-badge" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide w-fit"></span>
            </div>
            <button id="info-close" class="bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-slate-600 rounded-full p-2 transition-all active:scale-90 border border-slate-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Contenido --}}
        <div class="mb-5">
            <h3 id="info-title" class="text-xl font-black text-slate-900 leading-tight mb-1"></h3>
            <p id="info-desc" class="text-sm text-slate-500 font-medium leading-relaxed line-clamp-2"></p>
        </div>

        {{-- Footer --}}
        <div class="flex items-center gap-3">
            {{-- BOTÓN VOTO --}}
            <button id="info-vote-btn" class="flex items-center gap-2 px-4 py-3 rounded-2xl bg-slate-50 border border-slate-100 text-slate-600 transition-all active:scale-90 hover:bg-white hover:shadow-sm group">
                <svg id="info-heart-icon" class="w-5 h-5 text-slate-400 group-hover:text-red-500 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                </svg>
                <span id="info-votes" class="text-sm font-black tabular-nums">0</span>
            </button>

            {{-- BOTÓN DETALLES --}}
            <a id="info-link" href="#" class="flex-1 bg-[#0f172a] text-white text-sm font-bold py-3 px-4 rounded-2xl shadow-lg shadow-slate-900/20 active:scale-[0.98] transition-all hover:bg-black flex items-center justify-center gap-2">
                <span>Ver Detalles</span>
                <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </a>
        </div>
    </div>
</div>

<script>
(function() {
    const container = document.getElementById('info-point-container');
    const closeBtn = document.getElementById('info-close');
    const voteBtn = document.getElementById('info-vote-btn');
    const heartIcon = document.getElementById('info-heart-icon');
    const votesEl = document.getElementById('info-votes');
    const voteForm = document.getElementById('vote-form');
    
    let currentData = null;
    let isVisible = false;
    let isVoting = false;

    const statusConfig = {
        'pendiente':    { color: 'bg-yellow-400', badge: 'bg-yellow-50 text-yellow-600', text: 'Pendiente' },
        'en_revision':  { color: 'bg-blue-500',   badge: 'bg-blue-50 text-blue-600',     text: 'En Revisión' },
        'atendido':     { color: 'bg-green-500',  badge: 'bg-green-50 text-green-600',   text: 'Atendido' },
        'desestimado':  { color: 'bg-slate-400',  badge: 'bg-slate-50 text-slate-500',   text: 'Desestimado' },
        'default':      { color: 'bg-slate-400',  badge: 'bg-slate-50 text-slate-500',   text: 'Reportado' }
    };

    window.addEventListener('show-info-point', (e) => {
        currentData = {
            id: e.detail.id,
            titulo: e.detail.titulo,
            descripcion: e.detail.descripcion,
            estado: e.detail.estado,
            category: e.detail.category,
            votes_count: parseInt(e.detail.votes_count) || 0,
            has_voted: e.detail.has_voted === true || e.detail.has_voted === 'true' || e.detail.has_voted === 1
        };
        
        
        updateContent(currentData);
        show();
    });

    // 📡 Escuchar actualizaciones de votos en tiempo real
    window.addEventListener('vote-updated', (e) => {
        if (currentData && e.detail.report_id === currentData.id) {
            currentData.votes_count = e.detail.votes_count;
            votesEl.textContent = currentData.votes_count;
            // Animación sutil
            votesEl.classList.add('scale-125');
            setTimeout(() => votesEl.classList.remove('scale-125'), 200);
        }
    });

    // 📡 Escuchar cambios de estado en tiempo real
    window.addEventListener('status-updated', (e) => {
        if (currentData && e.detail.id === currentData.id) {
            currentData.estado = e.detail.new_status;
            const config = statusConfig[currentData.estado] || statusConfig['default'];
            document.getElementById('info-status-bar').className = `h-1.5 w-full ${config.color}`;
            document.getElementById('info-status-badge').className = `inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide w-fit ${config.badge}`;
            document.getElementById('info-status-badge').textContent = config.text;
        }
    });

    window.addEventListener('bottom-sheet-state', (e) => {
        if (isVisible) adjustPosition(e.detail.state);
    });

    closeBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        hide();
    });

    voteBtn?.addEventListener('click', async (e) => {
        e.stopPropagation();
        if (!currentData?.id || isVoting) return;

        isVoting = true;
        voteBtn.classList.add('scale-90');
        setTimeout(() => voteBtn.classList.remove('scale-90'), 150);

        // Guardar para rollback
        const prevVoted = currentData.has_voted;
        const prevCount = currentData.votes_count;

        // Optimistic UI
        currentData.has_voted = !prevVoted;
        currentData.votes_count = currentData.has_voted ? prevCount + 1 : Math.max(0, prevCount - 1);
        
        toggleVoteStyle(currentData.has_voted);
        votesEl.textContent = currentData.votes_count;

        try {
            // Crear ruta usando Blade (NO hardcoded)
            const formData = new FormData(voteForm);
            const url = `/denuncias/${currentData.id}/votar`;
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.status === 401) {
                window.location.href = '/login';
                return;
            }

            if (!response.ok) throw new Error('Error al votar');

            const result = await response.json();
            
            // Sincronizar con servidor
            currentData.has_voted = result.voted === true;
            currentData.votes_count = parseInt(result.votes_count) || 0;
            
            toggleVoteStyle(currentData.has_voted);
            votesEl.textContent = currentData.votes_count;
            


        } catch (err) { 
            console.error('❌ Error votando:', err);
            // Rollback
            currentData.has_voted = prevVoted;
            currentData.votes_count = prevCount;
            toggleVoteStyle(prevVoted);
            votesEl.textContent = prevCount;
        } finally {
            isVoting = false;
        }
    });

    function updateContent(data) {
        document.getElementById('info-title').textContent = data.titulo || 'Sin título';
        document.getElementById('info-desc').textContent = data.descripcion || 'Sin descripción.';
        document.getElementById('info-category').textContent = data.category || 'General';
        votesEl.textContent = data.votes_count;
        
        // Link usando ruta blade
        document.getElementById('info-link').href = `/denuncias/${data.id}`;

        const config = statusConfig[data.estado] || statusConfig['default'];
        document.getElementById('info-status-bar').className = `h-1.5 w-full ${config.color}`;
        document.getElementById('info-status-badge').className = `inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide w-fit ${config.badge}`;
        document.getElementById('info-status-badge').textContent = config.text;

        // Aplicar estado visual inicial CORRECTO
        toggleVoteStyle(data.has_voted);
        
    }

    function toggleVoteStyle(isVoted) {
        if (isVoted) {
            heartIcon.classList.remove('text-slate-400');
            heartIcon.classList.add('text-red-500', 'fill-current');
            voteBtn.classList.remove('bg-slate-50', 'border-slate-100', 'text-slate-600');
            voteBtn.classList.add('bg-red-50', 'border-red-200', 'text-red-500');
        } else {
            heartIcon.classList.remove('text-red-500', 'fill-current');
            heartIcon.classList.add('text-slate-400');
            voteBtn.classList.remove('bg-red-50', 'border-red-200', 'text-red-500');
            voteBtn.classList.add('bg-slate-50', 'border-slate-100', 'text-slate-600');
        }
    }

    function show() {
        isVisible = true;
        const sheetState = window.bottomSheetState || 'half';
        adjustPosition(sheetState);
        container.style.visibility = 'visible';
        container.style.opacity = '1';
        container.style.transform = 'translateY(0) scale(1)';
    }

    function hide() {
        isVisible = false;
        container.style.opacity = '0';
        container.style.transform = 'translateY(20px) scale(0.95)';
        setTimeout(() => { 
            if (!isVisible) {
                container.style.visibility = 'hidden'; 
                container.style.bottom = '-300px';
            }
        }, 400);
        window.dispatchEvent(new CustomEvent('close-info-point'));
    }

    function adjustPosition(sheetState) {
        const navbarHeight = 80; 
        const gap = 16;
        const positions = {
            'min': `calc(120px + ${navbarHeight}px + ${gap}px)`,
            'half': `calc(45vh + ${navbarHeight}px + ${gap}px)`,
            'full': '-500px'
        };
        
        if (sheetState === 'full') {
            if (isVisible) hide();
        } else {
            container.style.bottom = positions[sheetState] || positions['half'];
        }
    }

    window.hideInfoPoint = hide;
})();
</script>