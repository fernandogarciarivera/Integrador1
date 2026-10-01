<x-app-layout>
    <section class="cocina-page">
        <header class="cocina-header">
            <div>
                <p class="cocina-context">{{ $trabajador->local?->nombre ?? $trabajador->restaurante?->nombre }}</p>
                <h1>Despacho de pedidos</h1>
            </div>
            <div class="cocina-totals" aria-label="Resumen de pedidos">
                <span><strong>{{ $pendientes->count() }}</strong> Por entregar</span>
                <span><strong>{{ $entregados->count() }}</strong> Entregados hoy</span>
            </div>
        </header>

        @if (session('status'))
            <output class="cocina-notice">{{ session('status') }}</output>
        @endif

        <div class="cocina-board">
            <section class="cocina-column" aria-labelledby="despacho-pendientes-titulo">
                <header class="cocina-column__header">
                    <h2 id="despacho-pendientes-titulo"><span class="material-symbols-outlined" aria-hidden="true">receipt_long</span> Por entregar</h2>
                    <span>{{ $pendientes->count() }}</span>
                </header>
                <div class="cocina-column__body">
                    @forelse ($pendientes as $pedido)
                        <article class="cocina-card">
                            <header class="cocina-card__header">
                                <strong>#{{ $pedido->codigo_pedido }}</strong>
                                <time datetime="{{ $pedido->fecha_pedido->toIso8601String() }}">{{ $pedido->fecha_pedido->format('H:i') }}</time>
                            </header>
                            <div class="cocina-card__body">
                                <p class="cocina-card__meta">{{ $pedido->estado === 'LISTO' ? 'Listo' : 'Registrado' }} · {{ $pedido->tipo }} · {{ $pedido->cliente?->nombre ?? 'Cliente express' }}</p>
                                <ul>
                                    @foreach ($pedido->detalle_pedidos as $detalle)
                                        <li><strong>{{ $detalle->cantidad }}×</strong> {{ $detalle->detalleProducto ?? $detalle->productoDesc }}</li>
                                    @endforeach
                                </ul>
                                @if ($pedido->notas)
                                    <p class="cocina-card__notes">{{ $pedido->notas }}</p>
                                @endif
                            </div>
                            <form class="cocina-card__action" method="POST" action="{{ route('despacho.estado', $pedido) }}"
                                  onsubmit="return confirm('¿Confirma que desea marcar este pedido como entregado?')">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="estado" value="ENTREGADO">
                                <button type="submit">Marcar como entregado</button>
                            </form>
                        </article>
                    @empty
                        <p class="cocina-empty">No hay pedidos pendientes de entrega.</p>
                    @endforelse
                </div>
            </section>

            <section class="cocina-column cocina-column--ready" aria-labelledby="despacho-entregados-titulo">
                <header class="cocina-column__header">
                    <h2 id="despacho-entregados-titulo"><span class="material-symbols-outlined" aria-hidden="true">done_all</span> Entregados hoy</h2>
                    <span>{{ $entregados->count() }}</span>
                </header>
                <div class="cocina-column__body">
                    @forelse ($entregados as $pedido)
                        <article class="cocina-card cocina-card--ready">
                            <header class="cocina-card__header">
                                <strong>#{{ $pedido->codigo_pedido }}</strong>
                                <time datetime="{{ $pedido->fecha_pedido->toIso8601String() }}">Entregado</time>
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
                        <p class="cocina-empty">Aún no se entregaron pedidos hoy.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </section>
</x-app-layout>
