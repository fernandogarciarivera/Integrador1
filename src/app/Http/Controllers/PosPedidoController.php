<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CajaController;
use App\Models\Trabajador;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PosPedidoController extends CajaController
{
    public function store(Request $request)
    {
        $local = $request->attributes->get('api_local');
        $trabajador = $request->attributes->get('api_trabajador');

        $data = $request->validate([
            // Token ya validado por middleware; aquí validamos payload
            'trabajador_id'    => 'required|integer|exists:trabajadores,id',
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
            '_api_trabajador_id' => $trabajador->id,   // <-- ver punto 4
        ]);
        $interno->setUserResolver(fn() => $trabajador->user);
        $interno->headers->set('Accept', 'application/json');

        // Llamamos al método store de CajaController.
        // Necesitamos resolver el trabajador con Auth, así que lo hacemos
        // manualmente en el propio CajaController.
        return parent::store($interno);
    }
}
