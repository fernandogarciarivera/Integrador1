@php
    $editing = $local->exists;
    $selectedRestauranteId = old('restaurante_id', $local->restaurante_id ?? null);
    $selectedRestauranteNombre = $selectedRestauranteId ? $restaurantes->firstWhere('id', $selectedRestauranteId)?->nombre : null;
@endphp
<x-app-layout>
    <div class="worker-page">
        <div class="worker-shell">
            <div class="worker-heading">
                <div>
                    <h1>{{ $editing ? 'Editar local' : 'Nuevo local' }}</h1>
                    <p>Asocia el local a una empresa.</p>
                </div>
            </div>

            <div class="worker-form-grid">
                <section class="worker-panel">
                    <h2>Información</h2>
                    <form id="local-form" method="POST" action="{{ $editing ? route('locales.update', $local) : route('locales.store') }}">
                        @csrf
                        @if ($editing) @method('PUT') @endif
                        <div class="worker-fields">

                            <div class="worker-field">
                                <label class="worker-label" for="restaurante_input">Empresa</label>
                                <input class="worker-control" id="restaurante_input" list="restaurantes" value="{{ old('restaurante_id') ? $restaurantes->firstWhere('id', old('restaurante_id'))?->nombre : $selectedRestauranteNombre }}" placeholder="Escribe para buscar" autocomplete="off" required>
                                <datalist id="restaurantes">
                                    @foreach ($restaurantes as $restaurante)
                                        <option value="{{ $restaurante->nombre }}" data-id="{{ $restaurante->id }}">
                                    @endforeach
                                </datalist>
                                <input type="hidden" id="restaurante_id" name="restaurante_id" value="{{ $selectedRestauranteId ?? '' }}">
                            </div>

                            <div class="worker-field">
                                <label class="worker-label" for="nombre">Nombre</label>
                                <input class="worker-control" id="nombre" name="nombre" value="{{ old('nombre', $local->nombre) }}" required>
                            </div>
                            <div class="worker-field">
                                <label class="worker-label" for="codigo">Código</label>
                                <input class="worker-control" id="codigo" name="codigo" value="{{ old('codigo', $local->codigo) }}">
                            </div>
                            <div class="worker-field worker-field--full">
                                <label class="worker-label" for="direccion">Dirección</label>
                                <input class="worker-control" id="direccion" name="direccion" value="{{ old('direccion', $local->direccion) }}">
                            </div>
                            <div class="worker-field">
                                <label class="worker-label" for="estado">Estado</label>
                                <select class="worker-control" id="estado" name="estado" required>
                                    @foreach (['ACTIVO','INACTIVO'] as $e)
                                        <option value="{{ $e }}" @selected(old('estado', $local->estado ?? 'ACTIVO') === $e)>{{ $e }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if ($errors->any())
                            <div class="worker-errors">{{ $errors->first() }}</div>
                        @endif
                    </form>
                </section>

                @if ($editing)
                    <section class="worker-panel worker-access-panel">
                        <h2>Gestion de acceso</h2>
                        <div class="worker-access-list">
                            <div class="worker-access-item">
                                <div><strong>Acceso al sistema</strong><small>Regenerar token API del local.</small></div>
                                <form method="POST" action="{{ route('locales.regenerar-token', $local) }}" onsubmit="return confirm('¿Regenerar el token API de este local? El token anterior dejará de funcionar.')">
                                    @csrf
                                    <button type="submit" class="worker-edit" title="Regenerar token API" aria-label="Regenerar token">&#8635;</button>
                                </form>
                            </div>
                        </div>
                    </section>
                @endif

                <aside class="worker-panel">
                    <div class="worker-profile">
                        <div class="worker-avatar">{{ strtoupper(substr($local->nombre ?? 'L', 0, 1)) }}</div>
                        <h3>{{ $local->nombre ?? 'Nuevo local' }}</h3>
                        @if ($editing && $local->restaurante)
                            <span class="worker-role">{{ $local->restaurante->nombre }}</span>
                        @endif
                    </div>
                    <div class="worker-form-actions">
                        <button class="worker-button" type="submit" form="local-form">{{ $editing ? 'Guardar cambios' : 'Crear local' }}</button>
                        <a href="{{ route('locales.index') }}" class="worker-button worker-button--light">Cancelar</a>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const companyInput = document.querySelector('#restaurante_input');
            const companyId = document.querySelector('#restaurante_id');
            const companyList = document.querySelector('#restaurantes');

            const selectedId = (list, value) => [...list.options].find((option) => option.value.trim() === String(value).trim())?.dataset.id || '';

            const syncCompanyId = () => {
                if (!companyInput || !companyList || !companyId) return;
                const selected = selectedId(companyList, companyInput.value);
                companyId.value = selected;
            };

            if (companyInput && companyList && companyId) {
                companyInput.addEventListener('input', syncCompanyId);
                companyInput.addEventListener('change', syncCompanyId);
                companyInput.addEventListener('blur', syncCompanyId);

                if (companyInput.value && !companyId.value) {
                    syncCompanyId();
                }
            }
        })();
    </script>
</x-app-layout>
