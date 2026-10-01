<x-app-layout>
    <section class="cocina-page">
        <header class="cocina-header">
            <div>
                <p class="cocina-context">{{ $trabajador->local?->nombre ?? $trabajador->restaurante?->nombre }}</p>
                <h1>Pedidos de cocina</h1>
            </div>
            <div class="cocina-totals" aria-label="Resumen de pedidos">
                <span><strong>{{ $preparacion->count() }}</strong> En cola</span>
                <span><strong>{{ $listos->count() }}</strong> Listos</span>
            </div>
        </header>

        @if (session('status'))
            <output class="cocina-notice">{{ session('status') }}</output>
        @endif

        <div class="cocina-board">
            <section class="cocina-column" aria-labelledby="cocina-preparacion-titulo">
                <header class="cocina-column__header">
                    <h2 id="cocina-preparacion-titulo"><span class="material-symbols-outlined" aria-hidden="true">local_fire_department</span> En preparación</h2>
                    <span>{{ $preparacion->count() }}</span>
                </header>
                <div class="cocina-column__body">
                    @forelse ($preparacion as $pedido)
                        @php($preparando = $pedido->estado === 'PREPARANDO')
                        <article class="cocina-card {{ $preparando ? 'cocina-card--active' : '' }}">
                            <header class="cocina-card__header">
                                <strong>#{{ $pedido->codigo_pedido }}</strong>
                                <time datetime="{{ $pedido->fecha_pedido->toIso8601String() }}">{{ $pedido->fecha_pedido->format('H:i') }}</time>
                            </header>
                            <div class="cocina-card__body">
                                <p class="cocina-card__meta">{{ $pedido->estado === 'REGISTRADO' ? 'Registrado' : 'Preparando' }} · {{ $pedido->tipo }} · {{ $pedido->cliente?->nombre ?? 'Cliente express' }}</p>
                                <ul>
                                    @foreach ($pedido->detalle_pedidos as $detalle)
                                        <li>
                                            <strong>{{ $detalle->cantidad }}×</strong> {{ $detalle->detalleProducto ?? $detalle->productoDesc }}
                                            @if ($detalle->instrucciones_especiales)
                                                <small>{{ $detalle->instrucciones_especiales }}</small>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                                @if ($pedido->notas)
                                    <p class="cocina-card__notes">{{ $pedido->notas }}</p>
                                @endif
                            </div>
                            <form class="cocina-card__action" method="POST" action="{{ route('cocina.estado', $pedido) }}"
                                  onsubmit="return confirm('¿Confirma cambiar el pedido a {{ $preparando ? 'LISTO' : 'PREPARANDO' }}?')">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estado" value="{{ $preparando ? 'LISTO' : 'PREPARANDO' }}">
                                <button type="submit">{{ $preparando ? 'Marcar como listo' : 'Iniciar preparación' }}</button>
                            </form>
                        </article>
                    @empty
                        <p class="cocina-empty">No hay pedidos pendientes.</p>
                    @endforelse
                </div>
            </section>

            <section class="cocina-column cocina-column--ready" aria-labelledby="cocina-listos-titulo">
                <header class="cocina-column__header">
                    <h2 id="cocina-listos-titulo"><span class="material-symbols-outlined" aria-hidden="true">check_circle</span> Listos</h2>
                    <span>{{ $listos->count() }}</span>
                </header>
                <div class="cocina-column__body">
                    @forelse ($listos as $pedido)
                        <article class="cocina-card cocina-card--ready">
                            <header class="cocina-card__header">
                                <strong>#{{ $pedido->codigo_pedido }}</strong>
                                <span>Listo</span>
                            </header>
                            <div class="cocina-card__body">
                                <p class="cocina-card__meta">{{ $pedido->tipo }} · {{ $pedido->cliente?->nombre ?? 'Cliente express' }}</p>
                                <ul>
                                    @foreach ($pedido->detalle_pedidos as $detalle)
                                        <li><strong>{{ $detalle->cantidad }}×</strong> {{ $detalle->detalleProducto ?? $detalle->productoDesc }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </article>
                    @empty
                        <p class="cocina-empty">No hay pedidos listos.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </section>
</x-app-layout>
