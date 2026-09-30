{{-- resources/views/pedidos/seguimiento.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pedido #{{ $pedido->codigo_pedido }}</title>
    <style>
        body { font-family: system-ui, sans-serif; padding: 24px; max-width: 480px; margin: 0 auto; }
        .estado { font-size: 24px; font-weight: 700; margin: 12px 0; }
        .item { display: flex; justify-content: space-between; padding: 4px 0; }
        .total { font-weight: 700; border-top: 1px solid #eee; padding-top: 8px; margin-top: 8px; }
        .flash { background: #d0e1fb; padding: 10px; border-radius: 4px; margin-bottom: 16px; border: 1px solid #b7c8e1; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px; }
        .form-group input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .prefs { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 8px; }
        .prefs label { font-size: 12px; display: flex; align-items: center; gap: 4px; }
        button { background: #111827; color: white; border: none; padding: 10px 16px; border-radius: 4px; cursor: pointer; font-weight: 600; }
    </style>
</head>
<body>
    <h1>Pedido #{{ $pedido->codigo_pedido }}</h1>
    <p>{{ $pedido->locale->nombre }} — {{ $pedido->locale->direccion }}</p>

    {{-- Mensaje de éxito --}}
    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif

    <div class="estado">{{ $pedido->estado }}</div>
    <p>Registrado: {{ $pedido->fecha_pedido->format('H:i') }}</p>

    @if ($pedido->estado === 'LISTO')
        <p style="color:#0a0;"><strong>¡Tu pedido está listo para recoger!</strong></p>
    @endif

    <h2>Detalle</h2>
    @foreach ($pedido->detalle_pedidos as $d)
        <div class="item">
            <span>{{ $d->cantidad }}× {{ $d->productoDesc }}</span>
            <span>S/ {{ number_format($d->subtotal, 2) }}</span>
        </div>
    @endforeach
    <div class="item total">
        <span>Total</span>
        <span>S/ {{ number_format($pedido->total, 2) }}</span>
    </div>

    {{-- FORMULARIO PARA ASOCIAR CLIENTE (solo si no tiene cliente) --}}
    @if (!$pedido->cliente_id)
        <div style="margin-top: 32px; padding-top: 16px; border-top: 1px solid #eee;">
            <h2>¿Quieres recibir notificaciones?</h2>
            <p style="font-size: 14px; color: #666;">Asocia tus datos para recibir actualizaciones de tu pedido.</p>
            <form method="POST" action="{{ route('pedidos.asociar-cliente', $pedido->seguimiento_token) }}">
                @csrf
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" placeholder="Tu nombre">
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" placeholder="Ej: 999888777">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="tu@email.com">
                </div>
                <div class="form-group">
                    <label>Preferencias de notificación</label>
                    <div class="prefs">
                        @foreach (['SONIDO','VIBRACION','VISUAL','PUSH'] as $p)
                            <label>
                                <input type="checkbox" name="preferencias_notificacion[]" value="{{ $p }}">
                                {{ $p }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <button type="submit">Guardar y recibir notificaciones</button>
            </form>
        </div>
    @else
        <p style="margin-top: 24px; color: #0a0; font-size: 14px;">
            <strong>✓</strong> Este pedido ya está asociado a un cliente.
        </p>
    @endif

    <p style="margin-top:24px;color:#888;font-size:12px">
        Actualiza esta página para ver el estado actual.
    </p>
</body>
</html>