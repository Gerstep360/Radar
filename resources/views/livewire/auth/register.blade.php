<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="'Únete a la comunidad'" :description="'Crea tu cuenta y empieza a reportar en tu zona'" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-bold text-white/80 lg:text-slate-700 mb-1.5">Nombre
                    completo</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                    autocomplete="name" placeholder="Juan Pérez"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 lg:border-slate-300 bg-white/90 lg:bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all text-sm" />
                @error('name')
                    <p class="text-red-400 lg:text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-bold text-white/80 lg:text-slate-700 mb-1.5">Correo
                    electrónico</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                    autocomplete="email" placeholder="tu@email.com"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 lg:border-slate-300 bg-white/90 lg:bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all text-sm" />
                @error('email')
                    <p class="text-red-400 lg:text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password"
                    class="block text-sm font-bold text-white/80 lg:text-slate-700 mb-1.5">Contraseña</label>
                <input id="password" name="password" type="password" required autocomplete="new-password"
                    placeholder="Mínimo 8 caracteres"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 lg:border-slate-300 bg-white/90 lg:bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all text-sm" />
                @error('password')
                    <p class="text-red-400 lg:text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation"
                    class="block text-sm font-bold text-white/80 lg:text-slate-700 mb-1.5">Confirmar contraseña</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    autocomplete="new-password" placeholder="Repite tu contraseña"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 lg:border-slate-300 bg-white/90 lg:bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all text-sm" />
                @error('password_confirmation')
                    <p class="text-red-400 lg:text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <button type="submit" data-test="register-user-button"
                class="w-full py-3.5 px-6 bg-[#0f172a] text-white font-bold rounded-xl shadow-lg shadow-slate-900/20 hover:bg-slate-800 active:scale-[0.98] transition-all duration-200 text-sm tracking-wide">
                Crear mi cuenta
            </button>
        </form>

        <div class="text-center text-sm">
            <span class="text-white/50 lg:text-slate-500">¿Ya tienes una cuenta?</span>
            <a href="{{ route('login') }}" wire:navigate
                class="font-bold text-sky-400 lg:text-sky-600 hover:text-sky-300 lg:hover:text-sky-700 ml-1 transition-colors">
                Inicia sesión
            </a>
        </div>
    </div>
</x-layouts.auth>
