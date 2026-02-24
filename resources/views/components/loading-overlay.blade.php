<div x-data="{ loading: false }"
     @livewire:navigate.window="loading = true"
     @livewire:navigated.window="loading = false"
     x-show="loading"
     style="display: none;"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-zinc-900/40 backdrop-blur-sm transition-opacity"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
>
    <div class="flex flex-col items-center justify-center space-y-4 rounded-2xl bg-white/10 p-6 shadow-2xl ring-1 ring-white/20 backdrop-blur-md dark:bg-zinc-800/40">
        <svg class="h-14 w-14 animate-spin text-zinc-800 dark:text-zinc-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-lg font-semibold tracking-wide text-zinc-900 shadow-sm dark:text-white">Cargando...</span>
    </div>
</div>
