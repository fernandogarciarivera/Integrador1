<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CajaController;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\HistorialEstado;
use App\Models\Notificacione;
use App\Models\Pedido;
use App\Support\EstadoAccesoPedido;
use Illuminate\Support\Facades\DB;

class PosPedidoController extends CajaController
{
    /**
     * Crea un pedido para el local del token, con perfil POS.
     * Delegamos toda la lógica a CajaController@store.
     */
    public function store(Request $request)
    {
        $local = $request->attributes->get('api_local');

        $trabajador = Auth::user()?->trabajador()
            ->where('local_id', $local->id)
            ->where('rol', 'POS')
            ->where('activo', true)
            ->first();

        abort_unless($trabajador, 403, 'No autorizado para este local.');

        $data = $request->validate([
            'codigo_pedido'    => 'required|string|max:20|unique:pedidos,codigo_pedido',
            'tipo'             => ['required', Rule::in(['PRESENCIAL', 'PARA_LLEVAR'])],
            'cliente_id'       => 'nullable|exists:clientes,id',
            'notas'            => 'nullable|string|max:500',
            'items'            => 'required|array|min:1',
            'items.*.productoDesc'             => 'required|string|max:100',
            'items.*.cantidad'                 => 'required|integer|min:1',
            'items.*.precio_unitario'          => 'required|numeric|min:0',
            'items.*.instrucciones_especiales' => 'nullable|string|max:200',
        ]);

        // Construimos un Request interno "como si" viniera de la caja web
        // y delegamos TODA la lógica a CajaController@store.
        $interno = Request::create('', 'POST', [
            'cliente_id'    => $data['cliente_id'] ?? null,
            'codigo_pedido' => $data['codigo_pedido'],
            'tipo'          => $data['tipo'],
            'notas'         => $data['notas'] ?? null,
            'items'         => array_map(fn($i) => [
                'detalleProducto'          => $i['productoDesc'],
                'productoDesc'             => $i['productoDesc'],
                'cantidad'                 => $i['cantidad'],
                'precio_unitario'          => $i['precio_unitario'],
                'instrucciones_especiales' => $i['instrucciones_especiales'] ?? null,
            ], $data['items']),
            'trabajador_id' => $trabajador->id,
        ]);
        $interno->setUserResolver(fn() => $trabajador->user);
        $interno->headers->set('Accept', 'application/json');

        // Llamamos al método store de CajaController.
        // Necesitamos resolver el trabajador con Auth, así que lo hacemos
        // manualmente en el propio CajaController.
        return parent::store($interno);
    }

    /**
     * Cambia el estado de un pedido del local del token, identificándolo por código.
     * Permisos según `config/estado_acceso_pedido.php` (perfil POS).
     */
    public function cambiarEstadoPorCodigo(Request $request, string $codigoPedido)
    {
        $local = $request->attributes->get('api_local');

        $trabajador = Auth::user()?->trabajador()
            ->where('local_id', $local->id)
            ->where('rol', 'POS')
            ->where('activo', true)
            ->first();

        abort_unless($trabajador, 403, 'No autorizado para este local.');

        $data = $request->validate([
            'estado'        => ['required', Rule::in(EstadoAccesoPedido::estados())],
            'observaciones' => 'nullable|string|max:300',
        ]);

        // Buscar por local + codigo_pedido (el "número único" del local, no el id del servicio)
        $pedido = Pedido::where('local_id', $local->id)
            ->where('codigo_pedido', $codigoPedido)
            ->firstOrFail();

        // Validar permiso de transición con el módulo 'pos'
        abort_unless(
            EstadoAccesoPedido::puedeMoverEstado($trabajador->rol, $pedido->estado, $data['estado'], 'pos'),
            403,
            'Este perfil no puede asignar ese estado.'
        );

        DB::transaction(function () use ($pedido, $data, $trabajador) {
            $anterior = $pedido->estado;

            $pedido->update([
                'estado'                  => $data['estado'],
                'tiempo_preparacion_real' => $pedido->tiempo_preparacion_real
                    ?? now()->diffInMinutes($pedido->fecha_pedido),
                'fecha_expira_qr'         => in_array($data['estado'], ['ENTREGADO', 'CANCELADO'], true)
                    ? now()
                    : $pedido->fecha_expira_qr,
            ]);

            HistorialEstado::create([
                'pedido_id'       => $pedido->id,
                'trabajador_id'   => $trabajador->id,
                'estado_anterior' => $anterior,
                'estado_nuevo'    => $data['estado'],
                'fecha_cambio'    => now(),
                'observaciones'   => $data['observaciones'] ?? null,
            ]);

            Notificacione::create([
                'pedido_id'  => $pedido->id,
                'cliente_id' => $pedido->cliente_id,
                'tipo'       => 'VISUAL',
                'mensaje'    => "Pedido {$pedido->codigo_pedido} ahora está {$data['estado']}",
                'estado'     => 'ENVIADA',
            ]);
        });

        return response()->json([
            'ok'      => true,
            'codigo'  => $pedido->codigo_pedido,
            'estado'  => $pedido->estado,
        ]);
    }
}
