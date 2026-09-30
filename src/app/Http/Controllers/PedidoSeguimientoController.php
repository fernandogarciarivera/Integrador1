<?php

namespace App\Http\Controllers;

use App\Models\Pedido;

class PedidoSeguimientoController extends Controller
{
    public function show(string $token)
    {
        $pedido = Pedido::with(['locale:id,nombre,direccion', 'detalle_pedidos'])
            ->where('seguimiento_token', $token)
            ->firstOrFail();

        return view('pedidos.seguimiento', compact('pedido'));
    }
}
