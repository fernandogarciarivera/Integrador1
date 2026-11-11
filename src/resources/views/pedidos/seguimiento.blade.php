@php
    $trackingStates = [
        'REGISTRADO' => ['label' => 'Pedido recibido', 'title' => 'Recibimos tu pedido', 'description' => 'Tu pedido está registrado y pendiente de preparación.', 'icon' => 'receipt_long'],
        'PREPARANDO' => ['label' => 'En preparación', 'title' => 'Estamos preparando tu pedido', 'description' => 'El equipo de cocina está trabajando en tu orden.', 'icon' => 'skillet'],
        'LISTO' => ['label' => 'Listo para recoger', 'title' => '¡Tu pedido está listo!', 'description' => 'Acércate al mostrador de entrega para retirar tu orden.', 'icon' => 'notifications_active'],
        'ENTREGADO' => ['label' => 'Entregado', 'title' => '¡Que lo disfrutes!', 'description' => 'Tu pedido ya fue entregado. Gracias por tu visita.', 'icon' => 'check_circle'],
        'CANCELADO' => ['label' => 'Cancelado', 'title' => 'Pedido cancelado', 'description' => 'Consulta con el personal del local si necesitas más información.', 'icon' => 'cancel'],
    ];
    $trackingState = $trackingStates[$pedido->estado] ?? ['label' => $pedido->estado, 'title' => 'Estado de tu pedido', 'description' => '', 'icon' => 'receipt_long'];
    $trackingSteps = ['REGISTRADO', 'PREPARANDO', 'LISTO', 'ENTREGADO'];
    $trackingIndex = array_search($pedido->estado, $trackingSteps, true);
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pedido #{{ $pedido->codigo_pedido }}</title>
    @include('partials.fonts')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="buzzer-ui tracking-body">
    <header class="tracking-header">
        <div class="tracking-header__inner">
            <span class="design-icon-tile"><x-brand-mark /></span>
            <span class="brand-wordmark">Buzzer<span>Digital</span></span>
        </div>
    </header>
    <main class="tracking-shell">
    <section class="tracking-card tracking-order">
        <p class="design-eyebrow">Estado de tu orden</p>
        <h1>Pedido #{{ $pedido->codigo_pedido }}</h1>
        <p>{{ $pedido->locale->nombre }} — {{ $pedido->locale->direccion }}</p>
        <p>Registrado a las <strong>{{ $pedido->fecha_pedido->format('H:i') }}</strong></p>
    </section>

    {{-- Mensaje de éxito --}}
    @if (session('status'))
        <div class="caja-flash" role="status">{{ session('status') }}</div>
    @endif

    <section class="tracking-card tracking-state tracking-state--{{ strtolower($pedido->estado) }}">
        <div class="tracking-state__icon"><span class="material-symbols-outlined" aria-hidden="true">{{ $trackingState['icon'] }}</span></div>
        <span class="tracking-state__label">{{ $trackingState['label'] }}</span>
        <h2>{{ $trackingState['title'] }}</h2>
        <p>{{ $trackingState['description'] }}</p>
        @if ($pedido->estado === 'LISTO')
            <div class="tracking-ticket">
                <small>Muestra este número en despacho</small>
                <strong>#{{ $pedido->codigo_pedido }}</strong>
            </div>
        @endif
    </section>

    @if ($trackingIndex !== false)
        <section class="tracking-card">
            <h2><span class="material-symbols-outlined" aria-hidden="true">timeline</span> Seguimiento del pedido</h2>
            <ol class="tracking-steps">
                @foreach ($trackingSteps as $stepIndex => $step)
                    <li class="tracking-step {{ $stepIndex < $trackingIndex ? 'is-complete' : '' }} {{ $stepIndex === $trackingIndex ? 'is-current' : '' }}" @if ($stepIndex === $trackingIndex) aria-current="step" @endif>
                        <span class="tracking-step__dot"><span class="material-symbols-outlined" aria-hidden="true">{{ $stepIndex < $trackingIndex ? 'check' : $trackingStates[$step]['icon'] }}</span></span>
                        <div>
                            <strong>{{ $trackingStates[$step]['label'] }}</strong>
                            @if ($step === 'REGISTRADO')
                                <small>Registrado a las {{ $pedido->fecha_pedido->format('H:i') }}</small>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </section>
    @endif

    <section class="tracking-card">
    <h2><span class="material-symbols-outlined" aria-hidden="true">receipt_long</span> Detalle del pedido</h2>
    @foreach ($pedido->detalle_pedidos as $d)
        <div class="tracking-item">
            <span>{{ $d->cantidad }}× {{ $d->productoDesc }}</span>
            <span>S/ {{ number_format($d->subtotal, 2) }}</span>
        </div>
    @endforeach
    <div class="tracking-item tracking-total">
        <span>Total</span>
        <span>S/ {{ number_format($pedido->total, 2) }}</span>
    </div>
    </section>

    {{-- FORMULARIO PARA ASOCIAR CLIENTE (solo si no tiene cliente) --}}
    @if (!$pedido->cliente_id)
        <section class="tracking-card tracking-notifications">
            <h2>¿Quieres recibir notificaciones?</h2>
            <p>Asocia tus datos para recibir actualizaciones de tu pedido.</p>
            <form method="POST" action="{{ route('pedidos.asociar-cliente', $pedido->seguimiento_token) }}" class="tracking-form">
                @csrf
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input class="form-control" type="text" name="nombre" id="nombre" placeholder="Tu nombre">
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input class="form-control" type="text" name="telefono" id="telefono" placeholder="Ej: 999888777">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input class="form-control" type="email" name="email" id="email" placeholder="tu@email.com">
                </div>
                <fieldset class="form-group">
                    <legend>Preferencias de notificación</legend>
                    <div class="tracking-preferences">
                        @foreach (['SONIDO','VIBRACION','VISUAL','PUSH'] as $p)
                            <label>
                                <input type="checkbox" name="preferencias_notificacion[]" value="{{ $p }}">
                                {{ $p }}
                            </label>
                        @endforeach
                    </div>
                </fieldset>
                <button type="submit" class="btn btn-primary">Guardar y recibir notificaciones</button>
            </form>
        </section>
    @else
        <p class="tracking-associated">
            <strong>✓</strong> Este pedido ya está asociado a un cliente.
        </p>
    @endif

    <p class="tracking-refresh">
        Actualiza esta página para ver el estado actual.
    </p>
    </main>
</body>
</html>
