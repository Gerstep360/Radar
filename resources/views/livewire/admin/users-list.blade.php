<div>
    <div class="p-6 md:p-10 max-w-7xl mx-auto">
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-black text-slate-800">Gestión de Usuarios</h2>
                <p class="text-sm text-slate-500 font-medium mt-1">Control total sobre roles y accesos al sistema</p>
            </div>
            
            <div class="flex gap-4 items-center">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nombre o email..." 
                       class="px-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring- indigo-500 focus:border-indigo-500 shadow-sm">
                
                <select wire:model.live="roleFilter" class="px-4 py-2 border border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm bg-white">
                    <option value="">Todos los roles</option>
                    <option value="A">Administradores</option>
                    <option value="M">Moderadores</option>
                    <option value="U">Usuarios Normales</option>
                </select>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="mb-6 bg-emerald-50 text-emerald-600 px-6 py-4 rounded-2xl flex items-center justify-between border border-emerald-100 animate-in fade-in slide-in-from-top-4">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-rose-50 text-rose-600 px-6 py-4 rounded-2xl flex items-center justify-between border border-rose-100 animate-in fade-in slide-in-from-top-4">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <span class="font-bold text-sm">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 uppercase text-[10px] font-black tracking-widest text-slate-400">
                        <tr>
                            <th scope="col" class="px-6 py-4 rounded-tl-3xl">Usuario</th>
                            <th scope="col" class="px-6 py-4">Denuncias</th>
                            <th scope="col" class="px-6 py-4">Rol Actual</th>
                            <th scope="col" class="px-6 py-4 text-right rounded-tr-3xl">Acciones de Administrador</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $user)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-100 to-blue-50 flex items-center justify-center text-indigo-600 font-bold uppercase shadow-inner">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-black text-slate-800">{{ $user->name }}</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-black">
                                    {{ $user->reports_count ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $roleBadge = match($user->role) {
                                        'A' => ['bg-indigo-100 text-indigo-700 border-indigo-200', 'Administrador'],
                                        'M' => ['bg-blue-100 text-blue-700 border-blue-200', 'Moderador'],
                                        default => ['bg-slate-100 text-slate-600 border-slate-200', 'Usuario Normal'],
                                    };
                                @endphp
                                <span class="px-3 py-1.5 rounded-xl border font-black text-[10px] uppercase tracking-wider {{ $roleBadge[0] }}">
                                    {{ $roleBadge[1] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right flex justify-end gap-2 items-center">
                                @if($user->id !== auth()->id())
                                <div class="relative group">
                                    <select wire:change="changeRole({{ $user->id }}, $event.target.value)" 
                                            class="appearance-none font-bold text-xs bg-slate-50 border border-slate-200 text-slate-700 py-2 pl-4 pr-8 rounded-xl hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all cursor-pointer">
                                        <option value="U" {{ $user->role === 'U' ? 'selected' : '' }}>Usuario Normal</option>
                                        <option value="M" {{ $user->role === 'M' ? 'selected' : '' }}>Moderador</option>
                                        <option value="A" {{ $user->role === 'A' ? 'selected' : '' }}>Administrador</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                                    </div>
                                </div>
                                @else
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Es tu cuenta</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
