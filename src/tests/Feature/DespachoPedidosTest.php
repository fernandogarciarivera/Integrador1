<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\Pedido;
use App\Models\Restaurante;
use App\Models\Trabajadore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DespachoPedidosTest extends TestCase
{
    use RefreshDatabase;

    public function test_despacho_muestra_pendientes_por_antiguedad_y_registra_la_entrega(): void
    {
        $user = User::factory()->create();
        $user = User::query()->findOrFail($user->id);
        $restaurante = Restaurante::create(['nombre' => 'Restaurante Test', 'estado' => 'ACTIVO', 'plan' => 'BASICO']);
        $local = Locale::create(['restaurante_id' => $restaurante->id, 'nombre' => 'Local Test', 'estado' => 'ACTIVO']);
        $trabajador = Trabajadore::create([
            'user_id' => $user->id,
            'restaurante_id' => $restaurante->id,
            'local_id' => $local->id,
            'rol' => 'DESPACHO',
            'puesto' => 'Despacho',
            'activo' => true,
        ]);

        $antiguo = $this->crearPedido($local->id, $trabajador->id, 'OLD', 'REGISTRADO', now()->subMinutes(20));
        $this->crearPedido($local->id, $trabajador->id, 'READY', 'LISTO', now()->subMinutes(10));

        $this->actingAs($user)->get('/despacho')
            ->assertOk()
            ->assertSeeInOrder(['#OLD', '#READY']);

        $this->patch("/despacho/{$antiguo->id}/estado", ['estado' => 'ENTREGADO'])
            ->assertRedirect('/despacho');

        $this->assertDatabaseHas('pedidos', ['id' => $antiguo->id, 'estado' => 'ENTREGADO']);
        $this->assertDatabaseHas('historial_estados', [
            'pedido_id' => $antiguo->id,
            'estado_anterior' => 'REGISTRADO',
            'estado_nuevo' => 'ENTREGADO',
        ]);
    }

    private function crearPedido(int $localId, int $trabajadorId, string $codigo, string $estado, $fecha): Pedido
    {
        return Pedido::create([
            'local_id' => $localId,
            'cliente_id' => null,
            'trabajador_caja_id' => $trabajadorId,
            'codigo_pedido' => $codigo,
            'codigo_qr' => 'TEST-' . $codigo,
            'tipo' => 'PRESENCIAL',
            'estado' => $estado,
            'fecha_pedido' => $fecha,
            'total' => 10,
        ]);
    }
}
