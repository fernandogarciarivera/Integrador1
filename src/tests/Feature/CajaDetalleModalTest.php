<?php

namespace Tests\Feature;

use App\Models\HistorialEstado;
use App\Models\Locale;
use App\Models\Pedido;
use App\Models\Restaurante;
use App\Models\Trabajadore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CajaDetalleModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_caja_detail_endpoint_returns_order_data_and_qr(): void
    {
        $user = User::factory()->create();
        $restaurante = Restaurante::create([
            'nombre' => 'Restaurante Test',
            'estado' => 'ACTIVO',
            'plan' => 'BASICO',
        ]);
        $local = Locale::create([
            'restaurante_id' => $restaurante->id,
            'nombre' => 'Local Test',
            'estado' => 'ACTIVO',
        ]);
        $trabajador = Trabajadore::create([
            'user_id' => $user->id,
            'restaurante_id' => $restaurante->id,
            'local_id' => $local->id,
            'rol' => 'CAJA',
            'puesto' => 'Caja',
            'activo' => true,
        ]);
        $pedido = Pedido::create([
            'local_id' => $local->id,
            'cliente_id' => null,
            'trabajador_caja_id' => $trabajador->id,
            'codigo_pedido' => 'PO-TEST-001',
            'codigo_qr' => '10-PO-TEST-001',
            'tipo' => 'PRESENCIAL',
            'estado' => 'REGISTRADO',
            'fecha_pedido' => now(),
            'total' => 25.00,
        ]);
        HistorialEstado::create([
            'pedido_id' => $pedido->id,
            'trabajador_id' => $trabajador->id,
            'estado_nuevo' => 'REGISTRADO',
            'fecha_cambio' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/caja/' . $pedido->id . '/detalle');

        $response->assertOk();
        $response->assertJsonPath('codigo_qr', '10-PO-TEST-001');
        $response->assertJsonPath('estado', 'REGISTRADO');
    }
}
