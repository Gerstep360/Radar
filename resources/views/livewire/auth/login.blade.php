<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="'Bienvenido de vuelta'" :description="'Ingresa tus credenciales para acceder a Radar'" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-bold text-white/80 lg:text-slate-700 mb-1.5">Correo
                    electrónico</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                    autocomplete="email" placeholder="tu@email.com"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 lg:border-slate-300 bg-white/90 lg:bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all text-sm" />
                @error('email')
                    <p class="text-red-400 lg:text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password"
                        class="block text-sm font-bold text-white/80 lg:text-slate-700">Contraseña</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" wire:navigate
                            class="text-xs font-bold text-sky-400 lg:text-sky-600 hover:text-sky-300 lg:hover:text-sky-700 transition-colors">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                    placeholder="••••••••"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 lg:border-slate-300 bg-white/90 lg:bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all text-sm" />
                @error('password')
                    <p class="text-red-400 lg:text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500 transition" />
                <span class="text-sm text-white/70 lg:text-slate-600 font-medium">Recordar sesión</span>
            </label>

            <!-- Submit -->
            <button type="submit" data-test="login-button"
                class="w-full py-3.5 px-6 bg-[#0f172a] lg:bg-[#0f172a] text-white font-bold rounded-xl shadow-lg shadow-slate-900/20 hover:bg-slate-800 active:scale-[0.98] transition-all duration-200 text-sm tracking-wide">
                Iniciar sesión
            </button>
        </form>

        @if (Route::has('register'))
            <div class="text-center text-sm">
                <span class="text-white/50 lg:text-slate-500">¿No tienes una cuenta?</span>
                <a href="{{ route('register') }}" wire:navigate
                    class="font-bold text-sky-400 lg:text-sky-600 hover:text-sky-300 lg:hover:text-sky-700 ml-1 transition-colors">
                    Regístrate
                </a>
            </div>
        @endif
    </div>
</x-layouts.auth>
