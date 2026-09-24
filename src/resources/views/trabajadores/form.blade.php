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
                                <div id="crop-container" class="worker-crop" aria-live="polite">
                                    <div class="worker-crop__toolbar">
                                        <span class="worker-crop__label">Recorte</span>
                                        <button type="button" id="reset-crop" class="worker-button worker-button--light worker-button--small">Reiniciar recorte</button>
                                    </div>
                                    <div class="worker-crop__preview-wrap">
                                        <img id="crop-preview" src="" alt="Previsualización de la imagen">
                                        <div id="crop-overlay" class="worker-crop__overlay">
                                            <div id="crop-box" class="worker-crop__box"></div>
                                        </div>
                                    </div>
                                    <div class="worker-crop__hint">Arrastra para seleccionar la parte visible de la imagen antes de guardar. Tamaño máximo: 300×300.</div>
                                </div>
                                <input type="hidden" id="crop_x" name="crop_x" value="">
                                <input type="hidden" id="crop_y" name="crop_y" value="">
                                <input type="hidden" id="crop_width" name="crop_width" value="">
                                <input type="hidden" id="crop_height" name="crop_height" value="">
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
        const imageInput = document.querySelector('#imagen');
        const cropContainer = document.querySelector('#crop-container');
        const cropPreview = document.querySelector('#crop-preview');
        const cropOverlay = document.querySelector('#crop-overlay');
        const cropBox = document.querySelector('#crop-box');
        const cropX = document.querySelector('#crop_x');
        const cropY = document.querySelector('#crop_y');
        const cropWidth = document.querySelector('#crop_width');
        const cropHeight = document.querySelector('#crop_height');
        const resetCropButton = document.querySelector('#reset-crop');
        const MAX_CROP_SIZE = 300;

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

        const resetCrop = () => {
            cropX.value = '';
            cropY.value = '';
            cropWidth.value = '';
            cropHeight.value = '';
            cropBox.style.left = '0px';
            cropBox.style.top = '0px';
            cropBox.style.width = '0px';
            cropBox.style.height = '0px';
        };

        const updateCropBox = (x, y, width, height) => {
            const overlayRect = cropOverlay.getBoundingClientRect();
            const maxWidth = Math.max(overlayRect.width, 1);
            const maxHeight = Math.max(overlayRect.height, 1);
            const maxSize = Math.min(MAX_CROP_SIZE, Math.min(maxWidth, maxHeight));

            const clampedWidth = Math.min(Math.max(width, 0), maxSize);
            const clampedHeight = Math.min(Math.max(height, 0), maxSize);
            const boundedX = Math.min(Math.max(0, x), maxWidth - clampedWidth);
            const boundedY = Math.min(Math.max(0, y), maxHeight - clampedHeight);

            const finalWidth = Math.min(clampedWidth, Math.max(0, maxWidth - boundedX));
            const finalHeight = Math.min(clampedHeight, Math.max(0, maxHeight - boundedY));

            cropBox.style.left = boundedX + 'px';
            cropBox.style.top = boundedY + 'px';
            cropBox.style.width = finalWidth + 'px';
            cropBox.style.height = finalHeight + 'px';

            const naturalWidth = cropPreview.naturalWidth || 1;
            const naturalHeight = cropPreview.naturalHeight || 1;
            const displayedWidth = cropPreview.clientWidth || 1;
            const displayedHeight = cropPreview.clientHeight || 1;

            cropX.value = ((boundedX / displayedWidth) * naturalWidth).toFixed(2);
            cropY.value = ((boundedY / displayedHeight) * naturalHeight).toFixed(2);
            cropWidth.value = ((finalWidth / displayedWidth) * naturalWidth).toFixed(2);
            cropHeight.value = ((finalHeight / displayedHeight) * naturalHeight).toFixed(2);
        };

        let isDragging = false;
        let startX = 0;
        let startY = 0;

        const startCrop = (event) => {
            if (!cropPreview.src) return;
            event.preventDefault();
            isDragging = true;
            const overlayRect = cropOverlay.getBoundingClientRect();
            startX = event.clientX - overlayRect.left;
            startY = event.clientY - overlayRect.top;
            cropBox.style.left = '0px';
            cropBox.style.top = '0px';
            cropBox.style.width = '0px';
            cropBox.style.height = '0px';
        };

        const moveCrop = (event) => {
            if (!isDragging || !cropPreview.src) return;
            const overlayRect = cropOverlay.getBoundingClientRect();
            const currentX = Math.min(Math.max(event.clientX - overlayRect.left, 0), overlayRect.width);
            const currentY = Math.min(Math.max(event.clientY - overlayRect.top, 0), overlayRect.height);
            const x = Math.min(startX, currentX);
            const y = Math.min(startY, currentY);
            const width = Math.abs(currentX - startX);
            const height = Math.abs(currentY - startY);
            const size = Math.min(width, height);
            updateCropBox(x, y, size, size);
        };

        const endCrop = () => {
            if (!isDragging) return;
            isDragging = false;
            if (parseFloat(cropWidth.value) <= 0 || parseFloat(cropHeight.value) <= 0) {
                resetCrop();
            }
        };

        cropOverlay.addEventListener('pointerdown', startCrop);
        cropOverlay.addEventListener('pointermove', moveCrop);
        cropOverlay.addEventListener('pointerup', endCrop);
        cropOverlay.addEventListener('pointerleave', endCrop);
        resetCropButton.addEventListener('click', () => {
            resetCrop();
            if (cropPreview.src) {
                cropBox.style.left = '0px';
                cropBox.style.top = '0px';
                cropBox.style.width = '0px';
                cropBox.style.height = '0px';
            }
        });

        imageInput.addEventListener('change', (event) => {
            const file = event.target.files?.[0];
            if (!file) {
                cropContainer.classList.remove('is-visible');
                resetCrop();
                cropPreview.src = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = (loadEvent) => {
                cropPreview.src = loadEvent.target.result;
                cropContainer.classList.add('is-visible');
                resetCrop();
            };
            reader.readAsDataURL(file);
        });

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

