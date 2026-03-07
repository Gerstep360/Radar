<x-guest-layout>
    <x-slot name="headerDescription">Inicia sesion en tu cuenta</x-slot>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Correo electronico</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                autocomplete="username" placeholder="tu@email.com" class="form-input" />
            @error('email')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Contrasena</label>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                placeholder="Tu contrasena" class="form-input" />
            @error('password')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-row">
            <label class="form-check">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <span>Recordar sesion</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="form-link">Olvidaste tu contrasena?</a>
            @endif
        </div>

        <button type="submit" class="btn-submit">Iniciar sesion</button>
    </form>

    @if (Route::has('register'))
        <div class="auth-footer">
            No tienes cuenta? <a href="{{ route('register') }}">Registrate</a>
        </div>
    @endif
</x-guest-layout>
