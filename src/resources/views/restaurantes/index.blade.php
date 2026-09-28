<x-app-layout>
    <div class="worker-page">
        <div class="worker-shell">
            @if (session('status'))
                <div class="worker-flash">{{ session('status') }}</div>
            @endif

            <div class="worker-heading">
                <div>
                    <h1>Empresas / Restaurantes</h1>
                    <p>Administra las empresas del sistema.</p>
                </div>
                <a href="{{ route('restaurantes.create') }}" class="worker-button">+ Nueva empresa</a>
            </div>

            <form method="GET" action="{{ route('restaurantes.index') }}" class="worker-filter">
                <label class="worker-filter-field" for="restaurante">Empresa
                    <input class="worker-control" id="restaurante" type="search" name="restaurante" value="{{ request('restaurante') }}" placeholder="Nombre">
                </label>
                <label class="worker-filter-field" for="estado">Estado
                    <select class="worker-control" id="estado" name="estado">
                        <option value="">Todos</option>
                        <option value="ACTIVO"   @selected(request('estado') === 'ACTIVO')>Activo</option>
                        <option value="INACTIVO" @selected(request('estado') === 'INACTIVO')>Inactivo</option>
                    </select>
                </label>
                <button class="worker-button" type="submit">Buscar</button>
            </form>

            <div class="worker-list">
                @forelse ($restaurantes as $restaurante)
                    <article class="worker-row">
                        <div class="worker-avatar">{{ strtoupper(substr($restaurante->nombre, 0, 1)) }}</div>
                        <div>
                            <div class="worker-name">{{ $restaurante->nombre }}</div>
                            <div class="worker-detail">{{ $restaurante->email ?: 'Sin correo' }}</div>
                        </div>
                        <div class="worker-detail">{{ $restaurante->telefono ?: '—' }}</div>
                        <div class="worker-detail">{{ $restaurante->plan }}</div>
                        <span class="worker-status {{ $restaurante->estado !== 'ACTIVO' ? 'worker-status--off' : '' }}">
                            {{ $restaurante->estado }}
                        </span>
                        <a href="{{ route('restaurantes.edit', $restaurante) }}" class="worker-edit" title="Editar" aria-label="Editar">&#9998;</a>
                    </article>
                @empty
                    <div class="worker-empty">No hay empresas con esos filtros.</div>
                @endforelse
            </div>

            <div class="worker-pagination">{{ $restaurantes->links() }}</div>
        </div>
    </div>
</x-app-layout>