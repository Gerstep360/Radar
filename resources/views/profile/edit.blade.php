<x-app-layout>
    <div x-data="{ activeTab: 'feed' }" class="pb-32 bg-slate-50 relative w-full h-full overflow-y-auto no-scrollbar font-sans">
        <div class="bg-black pt-12 pb-6 px-6 relative overflow-hidden">
            {{-- Decorative circles --}}
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-blue-500/10 rounded-full blur-xl"></div>

            <div class="flex items-center gap-4 relative z-10">
                <div
                    class="w-16 h-16 rounded-full bg-gradient-to-tr from-blue-500 to-emerald-400 p-0.5 shadow-lg shadow-black/20">
                    <div
                        class="w-full h-full bg-black rounded-full border-2 border-black flex items-center justify-center text-white font-black text-xl">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                </div>
                <div class="text-white">
                    <h1 class="text-2xl font-black tracking-tight leading-tight">{{ $user->name }}</h1>
                    <p class="text-white/60 text-sm font-semibold">{{ $user->email }}</p>
                </div>
            </div>

            {{-- Stats --}}
            <div class="flex gap-6 mt-6 relative z-10 text-white">
                <div>
                    <div class="text-xs font-bold text-white/50 uppercase tracking-widest mb-0.5">Reportes</div>
                    <div class="text-lg font-black">{{ $misDenuncias->count() }}</div>
                </div>
                <div>
                    <div class="text-xs font-bold text-white/50 uppercase tracking-widest mb-0.5">Votos Req.</div>
                    <div class="text-lg font-black">{{ $misDenuncias->sum('votes_count') }}</div>
                </div>
            </div>
        </div>

        {{-- Custom Tabs Sticky Header --}}
        <div class="sticky top-0 z-40 bg-white/80 backdrop-blur-xl border-b border-slate-200/60 transition-shadow"
            :class="activeTab === 'feed' ? 'shadow-sm' : ''">
            <div class="flex w-full">
                <button @click="activeTab = 'feed'" class="flex-1 py-3.5 text-sm font-bold relative transition-colors"
                    :class="activeTab === 'feed' ? 'text-black' : 'text-slate-400'">
                    Mis Denuncias
                    <div class="absolute bottom-0 left-1/4 right-1/4 h-0.5 bg-black rounded-t-full transition-transform duration-300"
                        :class="activeTab === 'feed' ? 'scale-x-100 opacity-100' : 'scale-x-0 opacity-0'"></div>
                </button>
                <button @click="activeTab = 'settings'"
                    class="flex-1 py-3.5 text-sm font-bold relative transition-colors"
                    :class="activeTab === 'settings' ? 'text-black' : 'text-slate-400'">
                    Configuración
                    <div class="absolute bottom-0 left-1/4 right-1/4 h-0.5 bg-black rounded-t-full transition-transform duration-300"
                        :class="activeTab === 'settings' ? 'scale-x-100 opacity-100' : 'scale-x-0 opacity-0'"></div>
                </button>
            </div>
        </div>

        {{-- TAB CONTENT: Mis Denuncias (Feed) --}}
        <div x-show="activeTab === 'feed'" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="p-4 space-y-4">

            @if ($misDenuncias->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div
                        class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4 text-slate-300">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-slate-800">Cero actividad</h3>
                    <p class="text-sm font-semibold text-slate-400 max-w-[200px] mt-1">Aún no has reportado ninguna
                        denuncia en el radar.</p>
                </div>
            @else
                @foreach ($misDenuncias as $denuncia)
                    <div
                        class="bg-white rounded-[20px] shadow-[0_2px_10px_rgba(0,0,0,0.04)] overflow-hidden active:scale-[0.98] transition-transform">
                        <a href="{{ route('denuncias.show', $denuncia) }}" class="block p-4">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <div class="flex items-center gap-1.5 mb-1.5">
                                        <span
                                            class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $denuncia->category->name }}</span>
                                        <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                                        <span
                                            class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $denuncia->created_at->diffForHumans() }}</span>
                                    </div>
                                    <h3 class="font-extrabold text-black leading-tight">{{ $denuncia->title }}</h3>
                                </div>
                                <div class="shrink-0">
                                    @php
                                        $statusData = match ($denuncia->status) {
                                            'pendiente' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
                                            'en_revision' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
                                            'resuelto' => ['bg' => 'bg-green-100', 'text' => 'text-green-600'],
                                            default => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600'],
                                        };
                                    @endphp
                                    <span
                                        class="w-2.5 h-2.5 rounded-full block {{ str_replace('100', '500', $statusData['bg']) }} shadow-sm"></span>
                                </div>
                            </div>

                            @if ($denuncia->media->count() > 0)
                                <img src="{{ $denuncia->media->first()->url }}"
                                    class="w-full h-32 object-cover rounded-xl mt-3 mb-3">
                            @else
                                <p class="text-sm font-medium text-slate-500 line-clamp-2 mt-1 mb-3">
                                    {{ $denuncia->description }}</p>
                            @endif

                            <div class="flex items-center gap-4 border-t border-slate-100 pt-3 mt-1">
                                <div class="flex items-center gap-1 text-slate-400 font-bold text-xs">
                                    <svg fill="currentColor" viewBox="0 0 24 24"
                                        class="w-4 h-4 {{ $denuncia->votes_count > 0 ? 'text-rose-500' : '' }}">
                                        <path
                                            d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
                                    </svg>
                                    <span
                                        class="{{ $denuncia->votes_count > 0 ? 'text-black' : '' }}">{{ $denuncia->votes_count }}
                                        votos</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- TAB CONTENT: Settings --}}
        <div x-show="activeTab === 'settings'" style="display: none;"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="p-4 space-y-6">

            {{-- Personal Data --}}
            <div class="bg-white rounded-[20px] p-5 shadow-[0_2px_10px_rgba(0,0,0,0.04)]">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Datos Personales</h3>
                        <p class="text-[11px] font-semibold text-slate-400 tracking-wide">Actualiza tu información</p>
                    </div>
                </div>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('patch')
                    <div>
                        <label
                            class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Nombre</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full bg-slate-50 border-0 rounded-xl px-4 py-3 text-sm font-semibold text-black focus:ring-2 focus:ring-black/5"
                            required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">Correo
                            Electrónico</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full bg-slate-50 border-0 rounded-xl px-4 py-3 text-sm font-semibold text-black focus:ring-2 focus:ring-black/5"
                            required>
                    </div>
                    <div class="pt-2 flex justify-end">
                        <button type="submit"
                            class="bg-black text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md active:scale-95 transition-transform">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>

            {{-- Password --}}
            <div class="bg-white rounded-[20px] p-5 shadow-[0_2px_10px_rgba(0,0,0,0.04)]">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Contraseña</h3>
                        <p class="text-[11px] font-semibold text-slate-400 tracking-wide">Asegura tu cuenta</p>
                    </div>
                </div>

                <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('put')
                    <div>
                        <input type="password" name="current_password" placeholder="Contraseña actual"
                            class="w-full bg-slate-50 border-0 rounded-xl px-4 py-3 text-sm font-semibold text-black focus:ring-2 focus:ring-black/5"
                            required>
                    </div>
                    <div>
                        <input type="password" name="password" placeholder="Nueva contraseña"
                            class="w-full bg-slate-50 border-0 rounded-xl px-4 py-3 text-sm font-semibold text-black focus:ring-2 focus:ring-black/5"
                            required>
                    </div>
                    <div>
                        <input type="password" name="password_confirmation" placeholder="Confirmar nueva contraseña"
                            class="w-full bg-slate-50 border-0 rounded-xl px-4 py-3 text-sm font-semibold text-black focus:ring-2 focus:ring-black/5"
                            required>
                    </div>
                    <div class="pt-2 flex justify-end">
                        <button type="submit"
                            class="bg-black text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md active:scale-95 transition-transform">
                            Actualizar password
                        </button>
                    </div>
                </form>
            </div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}" class="px-2">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-between bg-rose-50 text-rose-600 px-5 py-4 rounded-[20px] font-bold text-sm active:scale-[0.98] transition-all">
                    <span>Cerrar Sesión</span>
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"
                        class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                </button>
            </form>

        </div>
    </div>
</x-app-layout>
