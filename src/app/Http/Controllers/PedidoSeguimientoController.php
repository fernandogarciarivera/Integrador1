<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoSeguimientoController extends Controller
{
    public function show(string $token)
    {
        $pedido = Pedido::with(['locale:id,nombre,direccion', 'detalle_pedidos'])
            ->where('seguimiento_token', $token)
            ->firstOrFail();

        return view('pedidos.seguimiento', compact('pedido'));
    }

    public function asociarCliente(Request $request, string $token)
    {
        $pedido = Pedido::where('seguimiento_token', $token)->firstOrFail();

        // Si el pedido ya tiene cliente, no hacemos nada (simplicidad).
        if ($pedido->cliente_id) {
            return redirect()->route('pedidos.seguimiento', $token)
                ->with('status', 'Este pedido ya tiene un cliente asociado.');
        }

        $data = $request->validate([
            'nombre'   => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20|unique:clientes,telefono',
            'email'    => 'nullable|email|max:100|unique:clientes,email',
            'preferencias_notificacion'   => 'nullable|array',
            'preferencias_notificacion.*' => 'in:SONIDO,VIBRACION,VISUAL,PUSH',
        ]);

        // La creación es mínima. Si no hay email, lo dejamos null.
        $cliente = Cliente::create([
            'nombre'   => $data['nombre'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'email'    => $data['email'] ?? null,
            'preferencias_notificacion' => $data['preferencias_notificacion'] ?? [],
        ]);

        $pedido->update(['cliente_id' => $cliente->id]);

        return redirect()->route('pedidos.seguimiento', $token)->with('status', 'Datos asociados correctamente.');
    }
}
