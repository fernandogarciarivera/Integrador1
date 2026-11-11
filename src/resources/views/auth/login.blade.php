<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="login-form">
        @csrf

        <!-- Email Address -->
        <div class="login-field">
            <x-input-label for="email" value="Correo electrónico" />
            <div class="login-input">
                <span class="material-symbols-outlined" aria-hidden="true">mail</span>
                <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" placeholder="usuario@restaurante.com" required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="login-field">
            <x-input-label for="password" value="Contraseña" />

            <div class="login-input">
                <span class="material-symbols-outlined" aria-hidden="true">lock</span>
                <x-text-input id="password" class="form-control"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="login-options">
            <label for="remember_me" class="login-remember">
                <input id="remember_me" type="checkbox" class="rounded border-theme-border text-brand focus:ring-brand" name="remember">
                <span>Recordarme</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>
        <button type="submit" class="btn btn-primary login-submit">Iniciar sesión</button>
    </form>
</x-guest-layout>
