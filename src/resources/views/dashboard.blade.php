<x-app-layout>
    <section class="dashboard-page">
        <header class="design-page-heading">
            <div>
                <p class="design-eyebrow">Panel principal</p>
                <h1>Dashboard</h1>
                <p>Tu espacio de trabajo para la gestión del restaurante.</p>
            </div>
            <span class="design-status"><span></span> Sesión iniciada</span>
        </header>

        <div class="dashboard-welcome card">
            <div class="dashboard-welcome__copy">
                <span class="design-icon-tile"><x-brand-mark /></span>
                <p class="design-eyebrow">Bienvenido a BuzzerDigital</p>
                <h2>Hola, {{ Auth::user()->name }}</h2>
                <p>Selecciona una opción del menú para continuar con tus tareas.</p>
            </div>
            <div class="dashboard-welcome__art" aria-hidden="true"><x-brand-mark /></div>
        </div>

        <div class="dashboard-account card">
            <span class="design-icon-tile"><span class="material-symbols-outlined" aria-hidden="true">account_circle</span></span>
            <div>
                <p class="design-eyebrow">Cuenta actual</p>
                <strong>{{ Auth::user()->name }}</strong>
                <p>{{ Auth::user()->email }}</p>
            </div>
        </div>
    </section>
</x-app-layout>
