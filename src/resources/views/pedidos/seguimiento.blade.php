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
    </style>
</head>
<body>
    <h1>Pedido #{{ $pedido->codigo_pedido }}</h1>
    <p>{{ $pedido->locale->nombre }} — {{ $pedido->locale->direccion }}</p>

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

    <p style="margin-top:24px;color:#888;font-size:12px">
        Actualiza esta página para ver el estado actual.
    </p>
</body>
</html>