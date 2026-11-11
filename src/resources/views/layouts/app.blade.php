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

        @include('partials.fonts')

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="buzzer-ui app-body">
        <div class="app-shell">
            <header class="app-header">
                <div class="app-header__brand">
                    <span class="app-header__logo"><x-brand-mark /></span>
                    <div class="app-header__copy">
                        <strong class="brand-wordmark"><span>Buzzer</span>Digital</strong>
                        <small>{{ $headerBrand }} &middot; {{ $headerLocation }}</small>
                    </div>
                </div>

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

                <div class="app-header__meta">
                    @if ($authUser)
                        <span class="app-header__avatar" aria-hidden="true">{{ mb_substr($authUser->name, 0, 1) }}</span>
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
