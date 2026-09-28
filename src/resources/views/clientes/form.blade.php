@php($editing = $cliente->exists)
@php($prefs = old('preferencias_notificacion', $cliente->preferencias_notificacion ?? []))
<x-app-layout>
    <div class="worker-page">
        <div class="worker-shell">
            <div class="worker-heading">
                <div>
                    <h1>{{ $editing ? 'Editar cliente' : 'Nuevo cliente' }}</h1>
                    <p>Datos de contacto y preferencias de notificación.</p>
                </div>
            </div>

            <div class="worker-form-grid">
                <section class="worker-panel">
                    <h2>Información</h2>
                    <form id="cliente-form" method="POST" action="{{ $editing ? route('clientes.update', $cliente) : route('clientes.store') }}">
                        @csrf
                        @if ($editing) @method('PUT') @endif
                        <div class="worker-fields">
                            <div class="worker-field worker-field--full">
                                <label class="worker-label" for="nombre">Nombre</label>
                                <input class="worker-control" id="nombre" name="nombre" value="{{ old('nombre', $cliente->nombre) }}">
                            </div>
                            <div class="worker-field">
                                <label class="worker-label" for="telefono">Teléfono</label>
                                <input class="worker-control" id="telefono" name="telefono" value="{{ old('telefono', $cliente->telefono) }}">
                            </div>
                            <div class="worker-field">
                                <label class="worker-label" for="email">Email</label>
                                <input class="worker-control" id="email" name="email" type="email" value="{{ old('email', $cliente->email) }}">
                            </div>
                            <div class="worker-field worker-field--full">
                                <label class="worker-label">Preferencias de notificación</label>
                                <div class="worker-access-list">
                                    @foreach (['SONIDO','VIBRACION','VISUAL','PUSH'] as $p)
                                        <label class="worker-access-item" style="cursor:pointer">
                                            <div>
                                                <strong>{{ $p }}</strong>
                                                <small>Notificar por {{ strtolower($p) }}</small>
                                            </div>
                                            <input type="checkbox" name="preferencias_notificacion[]" value="{{ $p }}"
                                                   @checked(in_array($p, (array) $prefs, true))>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @if ($errors->any())
                            <div class="worker-errors">{{ $errors->first() }}</div>
                        @endif
                    </form>
                </section>

                <aside class="worker-panel">
                    <div class="worker-profile">
                        <div class="worker-avatar">{{ strtoupper(substr($cliente->nombre ?? 'C', 0, 1)) }}</div>
                        <h3>{{ $cliente->nombre ?? 'Nuevo cliente' }}</h3>
                        @if ($editing && $cliente->telefono)
                            <span class="worker-role">{{ $cliente->telefono }}</span>
                        @endif
                    </div>
                    <div class="worker-form-actions">
                        <button class="worker-button" type="submit" form="cliente-form">{{ $editing ? 'Guardar cambios' : 'Crear cliente' }}</button>
                        <a href="{{ route('clientes.index') }}" class="worker-button worker-button--light">Cancelar</a>
                    </div>
                    @if ($editing)
                        <div class="worker-danger-action">
                            <form method="POST" action="{{ route('clientes.destroy', $cliente) }}"
                                  onsubmit="return confirm('¿Eliminar este cliente?')">
                                @csrf
                                @method('DELETE')
                                <button class="worker-button worker-button--danger" type="submit">Eliminar cliente</button>
                            </form>
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>