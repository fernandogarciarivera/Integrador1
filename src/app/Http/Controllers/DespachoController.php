<?php

namespace App\Http\Controllers;

use App\Models\HistorialEstado;
use App\Models\Notificacione;
use App\Models\Pedido;
use App\Support\EstadoAccesoPedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DespachoController extends Controller
{
    public function index()
    {
        $trabajador = Auth::user()?->trabajador;
        abort_unless($trabajador && $trabajador->activo, 403);
        abort_unless(in_array('ENTREGADO', EstadoAccesoPedido::permisosPorPerfil($trabajador->rol, 'despacho'), true), 403);

        $pedidos = Pedido::query()
            ->with(['cliente', 'detalle_pedidos'])
            ->where(fn($query) => $query
                ->whereIn('estado', ['REGISTRADO', 'LISTO'])
                ->orWhere(fn($query) => $query
                    ->where('estado', 'ENTREGADO')
                    ->whereHas('historial_estados', fn($query) => $query
                        ->where('estado_nuevo', 'ENTREGADO')
                        ->whereDate('fecha_cambio', today()))))
            ->whereHas('locale', fn($query) => $query
                ->where('restaurante_id', $trabajador->restaurante_id)
                ->when($trabajador->local_id !== null, fn($query) => $query->whereKey($trabajador->local_id)))
            ->orderBy('fecha_pedido')
            ->get();

        return view('despacho.index', [
            'pendientes' => $pedidos->whereIn('estado', ['REGISTRADO', 'LISTO']),
            'entregados' => $pedidos->where('estado', 'ENTREGADO'),
            'trabajador' => $trabajador,
        ]);
    }

    public function cambiarEstado(Request $request, Pedido $pedido)
    {
        $data = $request->validate([
            'estado' => ['required', Rule::in(['ENTREGADO'])],
        ]);

        $trabajador = $request->user()?->trabajador;
        abort_unless($trabajador && $trabajador->activo, 403);
        abort_unless(in_array($data['estado'], EstadoAccesoPedido::permisosPorPerfil($trabajador->rol, 'despacho'), true), 403);

        abort_unless($pedido->locale()
            ->where('restaurante_id', $trabajador->restaurante_id)
            ->when($trabajador->local_id !== null, fn($query) => $query->whereKey($trabajador->local_id))
            ->exists(), 403);

        DB::transaction(function () use ($pedido, $data, $trabajador) {
            $pedido = Pedido::query()->lockForUpdate()->findOrFail($pedido->id);
            abort_unless(
                in_array($pedido->estado, ['REGISTRADO', 'LISTO'], true),
                422,
                'El pedido ya cambió de estado o ya fue entregado.'
            );

            $estadoAnterior = $pedido->estado;
            $pedido->update(['estado' => $data['estado']]);

            HistorialEstado::create([
                'pedido_id' => $pedido->id,
                'trabajador_id' => $trabajador->id,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $data['estado'],
                'fecha_cambio' => now(),
            ]);

            Notificacione::create([
                'pedido_id' => $pedido->id,
                'cliente_id' => $pedido->cliente_id,
                'tipo' => 'VISUAL',
                'mensaje' => "Pedido {$pedido->codigo_pedido} ahora está {$data['estado']}",
                'estado' => 'ENVIADA',
            ]);
        });

        return to_route('despacho.index')->with('status', 'Pedido marcado como entregado.');
    }
}
