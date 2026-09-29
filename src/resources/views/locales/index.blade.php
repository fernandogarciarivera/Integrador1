@php
    $selectedRestauranteId = request('restaurante_id');
    $selectedRestauranteNombre = $selectedRestauranteId ? $restaurantes->firstWhere('id', $selectedRestauranteId)?->nombre : null;
@endphp
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
                
                <label class="worker-filter-field" for="restaurante_input">Empresa
                    <input class="worker-control" id="restaurante_input" list="restaurantes_filtro"
                           value="{{ $selectedRestauranteNombre }}"
                           placeholder="Escribe para buscar" autocomplete="off">
                    <datalist id="restaurantes_filtro">
                        @foreach ($restaurantes as $r)
                            <option value="{{ $r->nombre }}" data-id="{{ $r->id }}">
                        @endforeach
                    </datalist>
                    <input type="hidden" id="restaurante_id" name="restaurante_id" value="{{ $selectedRestauranteId ?? '' }}">
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
                        <div>
                            <div class="worker-detail">Código: {{ $local->codigo ?: '—' }}</div>
                            @if ($local->api_token)
                                <div class="worker-detail" style="font-family:monospace;font-size:10px;word-break:break-all;">
                                    Token: {{ $local->api_token }}
                                </div>
                            @else
                                <div class="worker-detail" style="color:#999;">Token: no generado</div>
                            @endif
                        </div>
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

    <script>
        (() => {
            const input = document.querySelector('#restaurante_input');
            const hidden = document.querySelector('#restaurante_id');
            const list  = document.querySelector('#restaurantes_filtro');
            if (!input || !hidden || !list) return;

            const sync = () => {
                const found = [...list.options].find(o => o.value.trim() === input.value.trim());
                hidden.value = found?.dataset.id ?? '';
            };

            input.addEventListener('input', sync);
            input.addEventListener('change', sync);
            input.addEventListener('blur', sync);
        })();
    </script>
</x-app-layout>