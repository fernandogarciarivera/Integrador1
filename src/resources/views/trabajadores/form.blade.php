@php($editing = $trabajador->exists)
<x-app-layout>
    <div class="worker-page">
        <div class="worker-shell">
            <div class="worker-heading">
                <div>
                    <h1>{{ $editing ? 'Editar trabajador' : 'Nuevo trabajador' }}</h1>
                    <p>Gestiona la informacion personal y el acceso al sistema.</p>
                </div>
            </div>

            <div class="worker-form-grid">
                <section class="worker-panel">
                    <h2>Informacion personal</h2>
                    <form id="worker-form" method="POST" enctype="multipart/form-data" action="{{ $editing ? route('trabajadores.update', $trabajador) : route('trabajadores.store') }}">
                        @csrf
                        @if ($editing) @method('PUT') @endif
                        <div class="worker-fields">
                            <div class="worker-field">
                                <label class="worker-label" for="name">Nombre completo</label>
                                <input class="worker-control" id="name" name="name" value="{{ old('name', $trabajador->user->name ?? '') }}" required>
                            </div>
                            <div class="worker-field">
                                <label class="worker-label" for="email">Usuario / Email</label>
                                <input class="worker-control" id="email" name="email" type="email" value="{{ old('email', $trabajador->user->email ?? '') }}" required>
                            </div>
                            <div class="worker-field">
                                <label class="worker-label" for="rol_input">Rol</label>
                                <input class="worker-control" id="rol_input" list="roles" value="{{ old('rol', $trabajador->rol ?? '') }}" placeholder="Escribe para buscar" required>
                                <datalist id="roles">
                                    @foreach ($perfiles as $perfil)
                                        <option value="{{ $perfil->perfil }}">
                                    @endforeach
                                </datalist>
                                <input type="hidden" id="rol" name="rol" value="{{ old('rol', $trabajador->rol ?? '') }}">
                            </div>
                            <div class="worker-field">
                                <label class="worker-label" for="restaurante_input">Empresa</label>
                                <input class="worker-control" id="restaurante_input" list="restaurantes" value="{{ old('restaurante_id') ? $restaurantes->firstWhere('id', old('restaurante_id'))?->nombre : $restaurantes->firstWhere('id', $trabajador->restaurante_id ?? null)?->nombre }}" placeholder="Escribe para buscar" autocomplete="off" required>
                                <datalist id="restaurantes">
                                    @foreach ($restaurantes as $restaurante)
                                        <option value="{{ $restaurante->nombre }}" data-id="{{ $restaurante->id }}">
                                    @endforeach
                                </datalist>
                                <input type="hidden" id="restaurante_id" name="restaurante_id" value="{{ old('restaurante_id', $trabajador->restaurante_id ?? '') }}">
                            </div>
                            <div class="worker-field">
                                <label class="worker-label" for="local_input">Local</label>
                                <input class="worker-control" id="local_input" list="locales" value="{{ old('local_id') ? $locales->firstWhere('id', old('local_id'))?->nombre : $locales->firstWhere('id', $trabajador->local_id ?? null)?->nombre }}" placeholder="Primero selecciona una empresa" autocomplete="off">
                                <datalist id="locales">
                                    @foreach ($locales as $local)
                                        <option value="{{ $local->nombre }}" data-id="{{ $local->id }}">
                                    @endforeach
                                </datalist>
                                <input type="hidden" id="local_id" name="local_id" value="{{ old('local_id', $trabajador->local_id ?? '') }}">
                            </div>
                            <div class="worker-field">
                                <label class="worker-label" for="puesto">Puesto</label>
                                <input class="worker-control" id="puesto" name="puesto" value="{{ old('puesto', $trabajador->puesto ?? '') }}">
                            </div>
                            <div class="worker-field">
                                <label class="worker-label" for="telefono">Telefono</label>
                                <input class="worker-control" id="telefono" name="telefono" value="{{ old('telefono', $trabajador->telefono ?? '') }}">
                            </div>
                            <div class="worker-field worker-field--full">
                                <label class="worker-label" for="imagen">Imagen del trabajador (opcional)</label>
                                <input class="worker-control" id="imagen" name="imagen" type="file" accept="image/*">
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
                                <div><strong>Contraseña del sistema</strong><small>Restablece la contraseña inicial del trabajador.</small></div>
                                <form method="POST" action="{{ route('trabajadores.reset-password', $trabajador) }}">
                                    @csrf
                                    <button class="worker-button worker-button--light" type="submit">&#8635; Restablecer</button>
                                </form>
                            </div>
                            <div class="worker-access-item">
                                <div><strong>Estado de acceso</strong><small>{{ $trabajador->activo ? 'El usuario puede acceder al sistema.' : 'El usuario no puede acceder al sistema.' }}</small></div>
                                <span class="worker-status {{ !$trabajador->activo ? 'worker-status--off' : '' }}">{{ $trabajador->activo ? 'Activo' : 'Inactivo' }}</span>
                            </div>
                        </div>
                    </section>
                @endif

                <aside class="worker-panel">
                    <div class="worker-profile">
                        <div class="worker-avatar">
                            @if ($trabajador->imagen_url)
                                <img src="{{ $trabajador->imagen_url }}" alt="{{ $trabajador->user->name ?? 'Trabajador' }}">
                            @else
                                &#128100;
                            @endif
                        </div>
                        <h3>{{ $trabajador->user->name ?? 'Nuevo trabajador' }}</h3>
                        @if ($editing)<span class="worker-role">{{ $trabajador->rol }}</span>@endif
                    </div>
                    <div class="worker-form-actions">
                        <button class="worker-button" type="submit" form="worker-form">{{ $editing ? 'Guardar cambios' : 'Crear trabajador' }}</button>
                        <a href="{{ route('trabajadores.index') }}" class="worker-button worker-button--light">Cancelar</a>
                    </div>
                    @if ($editing)
                        <div class="worker-danger-action">
                            <form method="POST" action="{{ route('trabajadores.toggle-activo', $trabajador) }}">
                                @csrf
                                <button class="worker-button worker-button--danger" type="submit">{{ $trabajador->activo ? 'Desactivar trabajador' : 'Activar trabajador' }}</button>
                            </form>
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    (() => {
        const companyInput = document.querySelector('#restaurante_input');
        const companyId = document.querySelector('#restaurante_id');
        const roleInput = document.querySelector('#rol_input');
        const roleId = document.querySelector('#rol');
        const roleList = document.querySelector('#roles');
        const localInput = document.querySelector('#local_input');
        const localId = document.querySelector('#local_id');
        const localList = document.querySelector('#locales');
        const companyList = document.querySelector('#restaurantes');

        const selectedId = (list, value) => [...list.options].find((option) => option.value === value)?.dataset.id || '';

        const loadLocales = async (id) => {
            localInput.value = '';
            localId.value = '';
            localList.replaceChildren();
            if (!id) return;
            const response = await fetch('{{ url('/restaurantes') }}/' + id + '/locales', { headers: { Accept: 'application/json' } });
            if (!response.ok) return;
            (await response.json()).forEach((local) => {
                const option = document.createElement('option');
                option.value = local.nombre;
                option.dataset.id = local.id;
                localList.append(option);
            });
        };

        companyInput.addEventListener('input', () => {
            companyId.value = selectedId(companyList, companyInput.value);
            loadLocales(companyId.value);
        });
        roleInput.addEventListener('input', () => {
            roleId.value = [...roleList.options].some((option) => option.value === roleInput.value) ? roleInput.value : '';
        });
        localInput.addEventListener('input', () => {
            localId.value = selectedId(localList, localInput.value);
        });
    })();
</script>

