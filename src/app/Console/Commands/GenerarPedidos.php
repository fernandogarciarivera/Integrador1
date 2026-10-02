<?php

namespace App\Console\Commands;

use App\Models\{DetallePedido, HistorialEstado, Locale, Notificacione, Pedido, Trabajador};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerarPedidos extends Command
{
    protected $signature = 'pedidos:generar
                            {cantidad=10 : Número de pedidos a crear}
                            {--local= : Solo un local (id)}
                            {--trabajador= : Solo un trabajador con perfil CAJA}
                            {--dry-run : Solo simula, no escribe en BD}';

    protected $description = 'Genera pedidos de prueba con detalles aleatorios usando un trabajador CAJA';

    private const PRODUCTOS = [
        'Pollo a la brasa',
        'Lomo saltado',
        'Ceviche',
        'Chaufa',
        'Anticucho',
        'Pizza familiar',
        'Hamburguesa',
        'Salchipapa',
        'Tallarín rojo',
        'Arroz con leche',
    ];

    public function handle(): int
    {
        $cantidad = (int) $this->argument('cantidad');
        $localId = $this->option('local');
        $trabajadorId = $this->option('trabajador');
        $dryRun = $this->option('dry-run');

        if ($cantidad < 1) {
            $this->error('La cantidad debe ser >= 1.');
            return self::FAILURE;
        }

        $trabajador = $this->elegirTrabajador($trabajadorId, $localId);

        if (! $trabajador) {
            $this->error('No hay trabajadores con perfil CAJA disponibles. Ejecuta primero: php artisan usuarios:generar-por-local');
            return self::FAILURE;
        }

        $this->info("Trabajador CAJA: #{$trabajador->id} ({$trabajador->user->email})");
        $this->line("Local: {$trabajador->locale->nombre} | Restaurante: {$trabajador->restaurante->nombre}");
        $this->line("Generando {$cantidad} pedido(s)" . ($dryRun ? ' [DRY-RUN]' : '') . '...');

        $bar = $this->output->createProgressBar($cantidad);
        $creados = 0;

        for ($i = 0; $i < $cantidad; $i++) {
            if (! $dryRun) {
                DB::transaction(fn() => $this->crearPedido($trabajador));
            }
            $creados++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✓ {$creados} pedido(s) generado(s).");

        return self::SUCCESS;
    }

    private function elegirTrabajador(?string $trabajadorId, ?string $localId): ?Trabajador
    {
        return Trabajador::with(['user', 'locale', 'restaurante'])
            ->where('rol', 'CAJA')
            ->where('activo', true)
            ->when($trabajadorId, fn($q) => $q->whereKey($trabajadorId))
            ->when($localId, fn($q) => $q->where('local_id', $localId))
            ->inRandomOrder()
            ->first();
    }

    private function crearPedido(Trabajador $trabajador): Pedido
    {
        $codigo = $this->codigoUnico();
        $items = $this->itemsAleatorios();
        $total = collect($items)->sum(fn($i) => $i['cantidad'] * $i['precio_unitario']);

        $pedido = Pedido::create([
            'local_id'                    => $trabajador->local_id,
            'cliente_id'                  => null,
            'trabajador_caja_id'          => $trabajador->id,
            'codigo_pedido'               => $codigo,
            'codigo_qr'                   => $trabajador->local_id . '-' . $codigo,
            'seguimiento_token'           => Str::random(40),
            'fecha_expira_qr'             => now()->addHour(),
            'tipo'                        => random_int(0, 1) ? 'PRESENCIAL' : 'PARA_LLEVAR',
            'estado'                      => 'REGISTRADO',
            'fecha_pedido'                => now(),
            'tiempo_preparacion_estimado' => 20,
            'total'                       => $total,
            'notas'                       => null,
        ]);

        foreach ($items as $item) {
            DetallePedido::create([
                'pedido_id'                => $pedido->id,
                'productoDesc'             => $item['productoDesc'],
                'cantidad'                 => $item['cantidad'],
                'precio_unitario'          => $item['precio_unitario'],
                'subtotal'                 => $item['cantidad'] * $item['precio_unitario'],
                'instrucciones_especiales' => null,
            ]);
        }

        HistorialEstado::create([
            'pedido_id'       => $pedido->id,
            'trabajador_id'   => $trabajador->id,
            'estado_anterior' => null,
            'estado_nuevo'    => 'REGISTRADO',
            'fecha_cambio'    => now(),
            'observaciones'   => 'Pedido generado por comando de QA',
        ]);

        Notificacione::create([
            'pedido_id'  => $pedido->id,
            'cliente_id' => null,
            'tipo'       => 'VISUAL',
            'mensaje'    => "Pedido {$pedido->codigo_pedido} registrado",
            'estado'     => 'ENVIADA',
        ]);

        return $pedido;
    }

    /** Código único con prefijo QA y timestamp corto. */
    private function codigoUnico(): string
    {
        do {
            $codigo = 'QA' . now()->format('mdHis') . random_int(100, 999);
        } while (Pedido::where('codigo_pedido', $codigo)->exists());

        return $codigo;
    }

    /** 1 a 5 items aleatorios, precio con decimales entre 1.00 y 10.00. */
    private function itemsAleatorios(): array
    {
        $n = random_int(1, 5);
        $items = [];

        for ($i = 0; $i < $n; $i++) {
            $items[] = [
                'productoDesc'    => self::PRODUCTOS[array_rand(self::PRODUCTOS)],
                'cantidad'        => random_int(1, 5),
                'precio_unitario' => random_int(100, 1000) / 100, // 1.00 a 10.00 con decimales
            ];
        }

        return $items;
    }
}
