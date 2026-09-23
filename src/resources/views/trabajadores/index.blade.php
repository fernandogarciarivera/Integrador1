<x-app-layout>
    <div class="worker-page">
        <div class="worker-shell">
            @if (session('status'))
                <div class="worker-flash">{{ session('status') }}</div>
            @endif

            <div class="worker-heading">
                <div>
                    <h1>Gestion de perfiles</h1>
                    <p>Administra el personal y sus accesos al sistema.</p>
                </div>
                <a href="{{ route('trabajadores.create') }}" class="worker-button">+ Nuevo trabajador</a>
            </div>

            <form method="GET" action="{{ route('trabajadores.index') }}" class="worker-filter">
                <label class="worker-filter-field" for="trabajador">Trabajador
                    <input class="worker-control" id="trabajador" type="search" name="trabajador" value="{{ request('trabajador') }}" placeholder="Nombre o correo">
                </label>
                <label class="worker-filter-field" for="restaurante_id">Empresa
                    <select class="worker-control" id="restaurante_id" name="restaurante_id">
                        <option value="">Todas las empresas</option>
                        @foreach ($restaurantes as $restaurante)
                            <option value="{{ $restaurante->id }}" @selected(request('restaurante_id') == $restaurante->id)>{{ $restaurante->nombre }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="worker-filter-field" for="local_id">Local
                    <select class="worker-control" id="local_id" name="local_id">
                        <option value="">Todos los locales</option>
                        @foreach ($locales as $local)
                            <option value="{{ $local->id }}" @selected(request('local_id') == $local->id)>{{ $local->nombre }}</option>
                        @endforeach
                    </select>
                </label>
                <button class="worker-button" type="submit">Buscar</button>
            </form>

            <div class="worker-list">
                @forelse ($trabajadores as $trabajador)
                    <article class="worker-row">
                        <div class="worker-avatar">
                            @if ($trabajador->imagen_url)
                                <img src="{{ $trabajador->imagen_url }}" alt="{{ $trabajador->user->name }}">
                            @else
                                {{ strtoupper(substr($trabajador->user->name, 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <div class="worker-name">{{ $trabajador->user->name }}</div>
                            <div class="worker-detail">{{ $trabajador->puesto ?: $trabajador->rol }}</div>
                        </div>
                        <div class="worker-detail">{{ $trabajador->restaurante->nombre }}</div>
                        <div class="worker-detail">{{ $trabajador->local?->nombre ?? 'Sin local' }}</div>
                        <span class="worker-status {{ !$trabajador->activo ? 'worker-status--off' : '' }}">
                            {{ $trabajador->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                        <a href="{{ route('trabajadores.edit', $trabajador) }}" class="worker-edit" title="Editar trabajador" aria-label="Editar trabajador">&#9998;</a>
                    </article>
                @empty
                    <div class="worker-empty">No hay trabajadores con esos filtros.</div>
                @endforelse
            </div>

            <div class="worker-pagination">{{ $trabajadores->links() }}</div>
        </div>
    </div>
</x-app-layout>

