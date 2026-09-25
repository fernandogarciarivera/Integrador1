@php
    $menuItems = $menuItems ?? collect();
    $trabajador = $trabajador ?? null;
    $authUser = $authUser ?? Auth::user();
    $headerBrand = $trabajador?->restaurante?->nombre ?? config('app.name', 'PekeTienda');
    $headerLocation = $trabajador?->local?->nombre ?? 'Panel principal';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0,0" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="app-body">
        <div class="app-shell">
            <header class="app-header">
                <div class="app-header__brand">
                    <span class="app-header__logo" aria-hidden="true">P</span>
                    <div class="app-header__copy">
                        <strong>{{ $headerBrand }}</strong>
                        <small>{{ $headerLocation }}</small>
                    </div>
                </div>

                <div class="app-header__meta">
                    @if ($authUser)
                        <div class="app-header__user">
                            <span>{{ $authUser->name }}</span>
                            @if ($trabajador && $trabajador->rol)
                                <small>{{ $trabajador->rol }}</small>
                            @endif
                        </div>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="app-header__logout">Cerrar sesión</button>
                    </form>
                </div>
            </header>

            <nav class="app-nav app-nav--desktop" aria-label="Navegación principal">
                @if ($menuItems->isEmpty())
                    <a href="{{ route('dashboard') }}" class="app-nav__link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">Dashboard</a>
                @else
                    @foreach ($menuItems as $menuItem)
                        @php($menuRoute = \App\View\Components\AppLayout::resolveMenuUrl($menuItem->controller ?? null, $menuItem->formulario ?? null))
                        @if ($menuRoute !== '#')
                            <a href="{{ $menuRoute }}" class="app-nav__link {{ request()->fullUrlIs($menuRoute . '*') ? 'is-active' : '' }}">
                                {{ $menuItem->formulario ?? 'Formulario' }}
                            </a>
                        @endif
                    @endforeach
                @endif
            </nav>

            @isset($header)
                <header class="app-page-header">
                    <div class="app-page-header__inner">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="app-main">
                {{ $slot }}
            </main>

            <nav class="app-nav app-nav--mobile" aria-label="Navegación móvil">
                @if ($menuItems->isEmpty())
                    <a href="{{ route('dashboard') }}" class="app-nav__link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">Dashboard</a>
                @else
                    @foreach ($menuItems as $menuItem)
                        @php($menuRoute = \App\View\Components\AppLayout::resolveMenuUrl($menuItem->controller ?? null, $menuItem->formulario ?? null))
                        @if ($menuRoute !== '#')
                            <a href="{{ $menuRoute }}" class="app-nav__link {{ request()->fullUrlIs($menuRoute . '*') ? 'is-active' : '' }}">
                                {{ $menuItem->formulario ?? 'Formulario' }}
                            </a>
                        @endif
                    @endforeach
                @endif
            </nav>
        </div>
    </body>
</html>
