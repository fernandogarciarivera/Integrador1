<x-app-layout>
    <div class="worker-page">
        <div class="worker-shell">
            @if (session('status'))
                <div class="worker-flash">{{ session('status') }}</div>
            @endif

            <div class="worker-heading">
                <div>
                    <h1>Locales</h1>
                    <p>Administra los locales por empresa.</p>
                </div>
                <a href="{{ route('locales.create') }}" class="worker-button">+ Nuevo local</a>
            </div>

            <form method="GET" action="{{ route('locales.index') }}" class="worker-filter">
                <label class="worker-filter-field" for="local">Local
                    <input class="worker-control" id="local" type="search" name="local" value="{{ request('local') }}" placeholder="Nombre del local">
                </label>
                <label class="worker-filter-field" for="restaurante_id">Empresa
                    <select class="worker-control" id="restaurante_id" name="restaurante_id">
                        <option value="">Todas</option>
                        @foreach ($restaurantes as $r)
                            <option value="{{ $r->id }}" @selected(request('restaurante_id') == $r->id)>{{ $r->nombre }}</option>
                        @endforeach
                    </select>
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
                @forelse ($locales as $local)
                    <article class="worker-row">
                        <div class="worker-avatar">{{ strtoupper(substr($local->nombre, 0, 1)) }}</div>
                        <div>
                            <div class="worker-name">{{ $local->nombre }}</div>
                            <div class="worker-detail">{{ $local->direccion ?: 'Sin dirección' }}</div>
                        </div>
                        <div class="worker-detail">{{ $local->restaurante?->nombre ?? '—' }}</div>
                        <div class="worker-detail">{{ $local->codigo ?: '—' }}</div>
                        <span class="worker-status {{ $local->estado !== 'ACTIVO' ? 'worker-status--off' : '' }}">
                            {{ $local->estado }}
                        </span>
                        <a href="{{ route('locales.edit', $local) }}" class="worker-edit" title="Editar" aria-label="Editar">&#9998;</a>
                    </article>
                @empty
                    <div class="worker-empty">No hay locales con esos filtros.</div>
                @endforelse
            </div>

            <div class="worker-pagination">{{ $locales->links() }}</div>
        </div>
    </div>
</x-app-layout>