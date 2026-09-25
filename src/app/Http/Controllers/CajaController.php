<?php

namespace App\Http\Controllers;

use App\Models\{Pedido, DetallePedido, HistorialEstado, Notificacione, Cliente};
use App\Models\Trabajador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};
use Illuminate\Validation\Rule;

class CajaController extends Controller
{
    /** Estados válidos (espejo del enum en BD). */
    private const ESTADOS = ['REGISTRADO', 'PREPARANDO', 'LISTO', 'ENTREGADO', 'CANCELADO'];

    public function index(Request $request)
    {
        $trabajador = Trabajador::with('local')->where('user_id', Auth::id())->firstOrFail();
        $localId = $trabajador->local_id;
        $accesoPedidos = Pedido::query()
            ->when($trabajador->restaurante_id !== null, fn($query) => $query->whereHas('locale', fn($locale) => $locale->where('restaurante_id', $trabajador->restaurante_id)))
            ->when($localId !== null, fn($query) => $query->where('local_id', $localId));

        $estado = $request->query('estado'); // filtro opcional
        $pedidos = (clone $accesoPedidos)->with('cliente:id,nombre')
            ->when($estado, fn($q) => $q->where('estado', $estado))
            ->latest('fecha_pedido')
            ->limit(50)
            ->get();

        $baseQuery = (clone $accesoPedidos)->whereDate('fecha_pedido', today());

        $metricas = [
            'total'      => $baseQuery->count(),
            'ventas'     => (float) $baseQuery->sum('total'),
            'en_proceso' => $baseQuery->clone()->whereIn('estado', ['REGISTRADO', 'PREPARANDO', 'LISTO'])->count(),
            'estados'    => [
                'REGISTRADO' => $baseQuery->clone()->where('estado', 'REGISTRADO')->count(),
                'PREPARANDO' => $baseQuery->clone()->where('estado', 'PREPARANDO')->count(),
                'LISTO'      => $baseQuery->clone()->where('estado', 'LISTO')->count(),
                'ENTREGADO'  => $baseQuery->clone()->where('estado', 'ENTREGADO')->count(),
                'CANCELADO'  => $baseQuery->clone()->where('estado', 'CANCELADO')->count(),
            ],
        ];

        return view('caja.form', compact('pedidos', 'metricas', 'trabajador', 'estado'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id'       => 'nullable|exists:clientes,id',
            'codigo_pedido'    => 'required|string|max:20|unique:pedidos,codigo_pedido',
            'tipo'             => ['required', Rule::in(['PRESENCIAL', 'PARA_LLEVAR'])],
            'notas'            => 'nullable|string|max:500',
            'items'            => 'required|array|min:1',
            'items.*.detalleProducto'      => 'required|string|max:100',
            'items.*.cantidad'             => 'required|integer|min:1',
            'items.*.precio_unitario'      => 'required|numeric|min:0',
            'items.*.instrucciones_especiales' => 'nullable|string|max:200',
        ]);

        $trabajador = Trabajador::where('user_id', Auth::id())->firstOrFail();

        $pedido = DB::transaction(function () use ($data, $trabajador) {
            $total = collect($data['items'])->sum(fn($i) => $i['cantidad'] * $i['precio_unitario']);

            $pedido = Pedido::create([
                'local_id'                    => $trabajador->local_id,
                'cliente_id'                  => $data['cliente_id'] ?? null,
                'trabajador_caja_id'          => $trabajador->id,
                'codigo_pedido'               => $data['codigo_pedido'],
                'codigo_qr'                   => $trabajador->local_id . '-' . $data['codigo_pedido'],
                'fecha_expira_qr'             => now()->addHour(),
                'tipo'                        => $data['tipo'],
                'estado'                      => 'REGISTRADO',
                'fecha_pedido'                => now(),
                'tiempo_preparacion_estimado' => 20,
                'total'                       => $total,
                'notas'                       => $data['notas'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                DetallePedido::create([
                    'pedido_id'     => $pedido->id,
                    'detalleProducto' => $item['detalleProducto'],
                    'cantidad'      => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal'      => $item['cantidad'] * $item['precio_unitario'],
                    'instrucciones_especiales' => $item['instrucciones_especiales'] ?? null,
                ]);
            }

            HistorialEstado::create([
                'pedido_id'       => $pedido->id,
                'trabajador_id'   => $trabajador->id,
                'estado_anterior' => null,
                'estado_nuevo'    => 'REGISTRADO',
                'fecha_cambio'    => now(),
                'observaciones'   => 'Pedido registrado en caja',
            ]);

            Notificacione::create([
                'pedido_id'  => $pedido->id,
                'cliente_id' => $pedido->cliente_id,
                'tipo'       => 'VISUAL',
                'mensaje'    => "Pedido {$pedido->codigo_pedido} registrado",
                'estado'     => 'ENVIADA',
            ]);

            return $pedido;
        });

        if ($request->wantsJson()) {
            return response()->json([
                'ok'        => true,
                'pedido_id' => $pedido->id,
                'codigo'    => $pedido->codigo_pedido,
                'qr'        => $pedido->codigo_qr,
                'expira'    => $pedido->fecha_expira_qr->toIso8601String(),
            ]);
        }

        return redirect()->route('caja.index')->with('status', "Pedido {$pedido->codigo_pedido} registrado.");
    }

    public function cambiarEstado(Request $request, Pedido $pedido)
    {
        $data = $request->validate([
            'estado'        => ['required', Rule::in(self::ESTADOS)],
            'observaciones' => 'nullable|string|max:300',
        ]);

        $trabajador = Trabajador::where('user_id', Auth::id())->firstOrFail();
        abort_if($pedido->local_id !== $trabajador->local_id, 403);

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

        return back()->with('status', "Estado actualizado a {$data['estado']}.");
    }

    public function show(Pedido $pedido)
    {
        $pedido->load(['cliente', 'detalle_pedidos', 'historial_estados.trabajadore.user']);
        return response()->json($pedido);
    }
}
