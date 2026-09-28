@php($editing = $restaurante->exists)
<x-app-layout>
    <div class="worker-page">
        <div class="worker-shell">
            <div class="worker-heading">
                <div>
                    <h1>{{ $editing ? 'Editar empresa' : 'Nueva empresa' }}</h1>
                    <p>Datos generales del restaurante.</p>
                </div>
            </div>

            <div class="worker-form-grid">
                <section class="worker-panel">
                    <h2>Información</h2>
                    <form id="restaurante-form" method="POST" action="{{ $editing ? route('restaurantes.update', $restaurante) : route('restaurantes.store') }}">
                        @csrf
                        @if ($editing) @method('PUT') @endif
                        <div class="worker-fields">
                            <div class="worker-field worker-field--full">
                                <label class="worker-label" for="nombre">Nombre</label>
                                <input class="worker-control" id="nombre" name="nombre" value="{{ old('nombre', $restaurante->nombre) }}" required>
                            </div>
                            <div class="worker-field worker-field--full">
                                <label class="worker-label" for="direccion">Dirección</label>
                                <input class="worker-control" id="direccion" name="direccion" value="{{ old('direccion', $restaurante->direccion) }}">
                            </div>
                            <div class="worker-field">
                                <label class="worker-label" for="telefono">Teléfono</label>
                                <input class="worker-control" id="telefono" name="telefono" value="{{ old('telefono', $restaurante->telefono) }}">
                            </div>
                            <div class="worker-field">
                                <label class="worker-label" for="email">Email</label>
                                <input class="worker-control" id="email" name="email" type="email" value="{{ old('email', $restaurante->email) }}">
                            </div>
                            <div class="worker-field">
                                <label class="worker-label" for="plan">Plan</label>
                                <select class="worker-control" id="plan" name="plan" required>
                                    @foreach (['BASICO','PRO','ENTERPRISE'] as $p)
                                        <option value="{{ $p }}" @selected(old('plan', $restaurante->plan ?? 'BASICO') === $p)>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="worker-field">
                                <label class="worker-label" for="estado">Estado</label>
                                <select class="worker-control" id="estado" name="estado" required>
                                    @foreach (['ACTIVO','INACTIVO'] as $e)
                                        <option value="{{ $e }}" @selected(old('estado', $restaurante->estado ?? 'ACTIVO') === $e)>{{ $e }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if ($errors->any())
                            <div class="worker-errors">{{ $errors->first() }}</div>
                        @endif
                    </form>
                </section>

                <aside class="worker-panel">
                    <div class="worker-profile">
                        <div class="worker-avatar">{{ strtoupper(substr($restaurante->nombre ?? 'R', 0, 1)) }}</div>
                        <h3>{{ $restaurante->nombre ?? 'Nueva empresa' }}</h3>
                    </div>
                    <div class="worker-form-actions">
                        <button class="worker-button" type="submit" form="restaurante-form">{{ $editing ? 'Guardar cambios' : 'Crear empresa' }}</button>
                        <a href="{{ route('restaurantes.index') }}" class="worker-button worker-button--light">Cancelar</a>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>