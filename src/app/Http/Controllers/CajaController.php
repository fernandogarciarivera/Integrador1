<?php

namespace App\Http\Controllers;

use App\Models\{Pedido, DetallePedido, HistorialEstado, Notificacione, Cliente};
use App\Models\Trabajador;
use App\Support\EstadoAccesoPedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Storage};
use Illuminate\Validation\Rule;
use chillerlan\QRCode\{QRCode, QROptions};

class CajaController extends Controller
{
    /** Estados válidos (espejo del enum en BD). */
    // private const ESTADOS = ['REGISTRADO', 'PREPARANDO', 'LISTO', 'ENTREGADO', 'CANCELADO'];

    private function trabajadorCajaActual(): Trabajador
    {
        //return Trabajador::with('local')->where('user_id', Auth::id())->firstOrFail();
        // Marca: si el Request trae `_api_trabajador_id`, lo usamos.
        // Esto permite que la API REST reuse TODO el flujo sin Auth::id().
        $id = request()->input('_api_trabajador_id');
        if ($id) {
            return Trabajador::with('local')->findOrFail($id);
        }
        return Trabajador::with('local')->where('user_id', Auth::id())->firstOrFail();
    }

    private function permisosEstadosPorPerfil(?string $perfil): array
    {
        return EstadoAccesoPedido::permisosPorPerfil($perfil);
    }

    private function validarCambioEstado(Trabajador $trabajador, Pedido $pedido, string $estadoDestino): void
    {
        $perfil = $trabajador->rol ?? null;
        $permitidos = $this->permisosEstadosPorPerfil($perfil);

        abort_if(! in_array($estadoDestino, $permitidos, true), 403, 'Este perfil no puede asignar ese estado.');

        if (EstadoAccesoPedido::pedidoFinalizado($pedido->estado)) {
            abort(403, 'No se puede modificar un pedido ya finalizado.');
        }
    }

    private function pedidosDelTrabajador(Trabajador $trabajador)
    {
        return Pedido::query()
            ->where('trabajador_caja_id', $trabajador->id)
            ->when($trabajador->local_id !== null, fn($query) => $query->where('local_id', $trabajador->local_id));
    }

    public function index(Request $request)
    {
        $trabajador = $this->trabajadorCajaActual();
        $accesoPedidos = $this->pedidosDelTrabajador($trabajador);

        $estado = $request->query('estado');

        $pedidos = (clone $accesoPedidos)->with('cliente:id,nombre')
            ->when($estado, fn($q) => $q->where('estado', $estado))
            ->latest('fecha_pedido')
            ->limit(50)
            ->get();

        $baseQuery = (clone $accesoPedidos)->whereDate('fecha_pedido', today());

        $metricas = [
            'total'      => $baseQuery->count(),
            'ventas'     => (float) $baseQuery->sum('total'),
            'en_proceso' => $baseQuery->clone()->whereIn('estado', config('estado_acceso_pedido.proceso'))->count(),
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
            'items.*.productoDesc' => 'nullable|string|max:100',
            'items.*.detalleProducto' => 'nullable|string|max:100',
            'items.*.cantidad'             => 'required|integer|min:1',
            'items.*.precio_unitario'      => 'required|numeric|min:0',
            'items.*.instrucciones_especiales' => 'nullable|string|max:200',
        ]);

        $data['items'] = array_map(function (array $item): array {
            $descripcion = trim((string) ($item['detalleProducto'] ?? $item['productoDesc'] ?? ''));

            if ($descripcion === '') {
                abort(422, 'Cada ítem debe incluir una descripción del producto.');
            }

            return [
                'detalleProducto' => $descripcion,
                'productoDesc' => $descripcion,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'instrucciones_especiales' => $item['instrucciones_especiales'] ?? null,
            ];
        }, $data['items']);

        $pedido = $this->registrarPedido($data, $this->trabajadorCajaActual());

        if ($request->wantsJson()) {
            return response()->json($this->respuestaPedido($pedido));
        }

        return redirect()->route('caja.index')->with('status', "Pedido {$pedido->codigo_pedido} registrado.");
    }

    /** Registro compartido por la caja web y la API POS. */
    protected function registrarPedido(array $data, Trabajador $trabajador): Pedido
    {
        return DB::transaction(function () use ($data, $trabajador) {
            // Si no llega código (POS), se genera el siguiente correlativo; lockForUpdate evita duplicados entre cajas.
            $codigo = $data['codigo_pedido']
                ?? (string) ((int) Pedido::withTrashed()->lockForUpdate()->max(DB::raw('CAST(codigo_pedido AS UNSIGNED)')) + 1);

            $total = collect($data['items'])->sum(fn($i) => $i['cantidad'] * $i['precio_unitario']);

            $pedido = Pedido::create([
                'local_id'                    => $trabajador->local_id,
                'cliente_id'                  => $data['cliente_id'] ?? null,
                'trabajador_caja_id'          => $trabajador->id,
                'codigo_pedido'               => $codigo,
                'codigo_qr'                   => $trabajador->local_id . '-' . $codigo, // QR de seguimiento (no secreto)
                'seguimiento_token'           => \Illuminate\Support\Str::random(40), // token único para seguimiento de pedidos
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
                    'productoDesc' => $item['productoDesc'],
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

            /**
             * se genera un QR que apunta a la ruta de seguimiento del pedido, usando el token único de seguimiento.
             * La ruta de seguimiento es algo como: /pedidos/seguimiento/{token}
             * El QR se guarda en storage/app/public/qr/{pedido_id}.svg y se almacena la URL pública en el campo imagen_qr del pedido.
             * El token de seguimiento es único y se genera al crear el pedido
             */
            $urlSeguimiento = route('pedidos.seguimiento', ['token' => $pedido->seguimiento_token]);
            $ruta = "qr/{$pedido->id}.svg";
            Storage::disk('public')->put($ruta, (new QRCode(new QROptions(['outputBase64' => false])))->render($urlSeguimiento));
            $pedido->update(['imagen_qr' => Storage::url($ruta)]);

            return $pedido;
        });
    }

    /** Respuesta JSON con el QR: URL de la imagen y la misma imagen en base64 para imprimir sin otra llamada. */
    protected function respuestaPedido(Pedido $pedido): array
    {
        return [
            'ok'         => true,
            'pedido_id'  => $pedido->id,
            'codigo'     => $pedido->codigo_pedido,
            'qr'         => $pedido->codigo_qr,
            'qr_url'     => asset($pedido->imagen_qr),
            'qr_base64'  => 'data:image/svg+xml;base64,' . base64_encode(Storage::disk('public')->get("qr/{$pedido->id}.svg")),
            'seguimiento_url' => route('pedidos.seguimiento', ['token' => $pedido->seguimiento_token]), // ← nuevo
            'expira'     => $pedido->fecha_expira_qr->toIso8601String(),
        ];
    }

    public function cambiarEstado(Request $request, Pedido $pedido)
    {
        $data = $request->validate([
            'estado'        => ['required', Rule::in(EstadoAccesoPedido::estados())],
            'observaciones' => 'nullable|string|max:300',
        ]);

        $trabajador = $this->trabajadorCajaActual();
        abort_if($pedido->trabajador_caja_id !== $trabajador->id, 403);
        abort_if($pedido->local_id !== $trabajador->local_id, 403);
        $this->validarCambioEstado($trabajador, $pedido, $data['estado']);

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
        $trabajador = $this->trabajadorCajaActual();
        abort_if($pedido->trabajador_caja_id !== $trabajador->id, 403);

        $pedido->load(['cliente', 'detalle_pedidos', 'historial_estados.trabajadore.user']);
        return response()->json($pedido);
    }
}
