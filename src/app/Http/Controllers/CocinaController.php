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

class CocinaController extends Controller
{
    public function index()
    {
        $trabajador = Auth::user()?->trabajador;
        abort_unless($trabajador && $trabajador->activo, 403);

        $estadosPermitidos = EstadoAccesoPedido::permisosPorPerfil($trabajador->rol, 'cocina');
        abort_unless(in_array('PREPARANDO', $estadosPermitidos, true)
            && in_array('LISTO', $estadosPermitidos, true), 403);

        $pedidos = Pedido::query()
            ->with(['cliente', 'detalle_pedidos'])
            ->whereIn('estado', ['REGISTRADO', 'PREPARANDO', 'LISTO'])
            ->whereHas('locale', fn($query) => $query
                ->where('restaurante_id', $trabajador->restaurante_id)
                ->when($trabajador->local_id !== null, fn($query) => $query->whereKey($trabajador->local_id)))
            ->orderByRaw("CASE estado WHEN 'PREPARANDO' THEN 0 WHEN 'REGISTRADO' THEN 1 ELSE 2 END")
            ->orderBy('fecha_pedido')
            ->get();

        return view('cocina.index', [
            'preparacion' => $pedidos->whereIn('estado', ['REGISTRADO', 'PREPARANDO']),
            'listos' => $pedidos->where('estado', 'LISTO'),
            'trabajador' => $trabajador,
        ]);
    }

    public function cambiarEstado(Request $request, Pedido $pedido)
    {
        $data = $request->validate([
            'estado' => ['required', Rule::in(['PREPARANDO', 'LISTO'])],
        ]);

        $trabajador = $request->user()?->trabajador;
        abort_unless($trabajador && $trabajador->activo, 403);

        $estadosPermitidos = EstadoAccesoPedido::permisosPorPerfil($trabajador->rol, 'cocina');
        abort_unless(in_array($data['estado'], $estadosPermitidos, true), 403);

        abort_unless($pedido->locale()
            ->where('restaurante_id', $trabajador->restaurante_id)
            ->when($trabajador->local_id !== null, fn($query) => $query->whereKey($trabajador->local_id))
            ->exists(), 403);

        DB::transaction(function () use ($pedido, $data, $trabajador) {
            $pedido = Pedido::query()->lockForUpdate()->findOrFail($pedido->id);
            $siguienteEstado = ['REGISTRADO' => 'PREPARANDO', 'PREPARANDO' => 'LISTO'];
            abort_unless(($siguienteEstado[$pedido->estado] ?? null) === $data['estado'],
                422,
                'El pedido ya cambió de estado o la transición no es válida.'
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

        return to_route('cocina.index')->with('status', "Pedido actualizado a {$data['estado']}.");
    }
}
