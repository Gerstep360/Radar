<x-guest-layout>
    <x-slot name="headerDescription">Crea tu cuenta para reportar</x-slot>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">Nombre completo</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                autocomplete="name" placeholder="Juan Perez" class="form-input" />
            @error('name')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Correo electronico</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                autocomplete="username" placeholder="tu@email.com" class="form-input" />
            @error('email')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Contrasena</label>
            <input id="password" name="password" type="password" required autocomplete="new-password"
                placeholder="Minimo 8 caracteres" class="form-input" />
            @error('password')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirmar contrasena</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                autocomplete="new-password" placeholder="Repite tu contrasena" class="form-input" />
            @error('password_confirmation')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-submit">Crear mi cuenta</button>
    </form>

    <div class="auth-footer">
        Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesion</a>
    </div>
</x-guest-layout>
