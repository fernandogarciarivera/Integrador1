<x-app-layout>
    <div class="worker-page">
        <div class="worker-shell">
            @if (session('status'))
                <div class="worker-flash">{{ session('status') }}</div>
            @endif

            <div class="worker-heading">
                <div>
                    <h1>Clientes</h1>
                    <p>Administra los clientes registrados.</p>
                </div>
                <a href="{{ route('clientes.create') }}" class="worker-button">+ Nuevo cliente</a>
            </div>

            <form method="GET" action="{{ route('clientes.index') }}" class="worker-filter">
                <label class="worker-filter-field" for="cliente">Cliente
                    <input class="worker-control" id="cliente" type="search" name="cliente" value="{{ request('cliente') }}" placeholder="Nombre, teléfono o email">
                </label>
                <button class="worker-button" type="submit">Buscar</button>
            </form>

            <div class="worker-list">
                @forelse ($clientes as $cliente)
                    <article class="worker-row">
                        <div class="worker-avatar">
                            {{ strtoupper(substr($cliente->nombre ?? $cliente->telefono ?? 'C', 0, 1)) }}
                        </div>
                        <div>
                            <div class="worker-name">{{ $cliente->nombre ?: 'Sin nombre' }}</div>
                            <div class="worker-detail">{{ $cliente->email ?: 'Sin email' }}</div>
                        </div>
                        <div class="worker-detail">{{ $cliente->telefono ?: '—' }}</div>
                        <div class="worker-detail">
                            {{ $cliente->fecha_ultima_visita?->format('d/m/Y H:i') ?? '—' }}
                        </div>
                        <span class="worker-status {{ $cliente->fecha_ultima_visita ? '' : 'worker-status--off' }}">
                            {{ $cliente->fecha_ultima_visita ? 'Activo' : 'Nuevo' }}
                        </span>
                        <a href="{{ route('clientes.edit', $cliente) }}" class="worker-edit" title="Editar" aria-label="Editar">&#9998;</a>
                    </article>
                @empty
                    <div class="worker-empty">No hay clientes con esos filtros.</div>
                @endforelse
            </div>

            <div class="worker-pagination">{{ $clientes->links() }}</div>
        </div>
    </div>
</x-app-layout>