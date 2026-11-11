<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @include('partials.fonts')

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="buzzer-ui guest-body font-sans antialiased">
        <main class="guest-shell">
            <header class="guest-brand">
                <a href="/" class="brand-wordmark"><span>Buzzer</span>Digital</a>
                <p>Gestión operativa de tu restaurante</p>
            </header>

            <div class="guest-panel">
                {{ $slot }}
            </div>
        </main>
    </body>
</html>
