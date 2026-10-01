<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\Pedido;
use App\Models\Restaurante;
use App\Models\Trabajadore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CocinaPedidosTest extends TestCase
{
    use RefreshDatabase;

    public function test_cocina_muestra_la_cola_y_solo_permite_avanzar_al_siguiente_estado(): void
    {
        $user = User::factory()->create();
        $restaurante = Restaurante::create(['nombre' => 'Restaurante Test', 'estado' => 'ACTIVO', 'plan' => 'BASICO']);
        $local = Locale::create(['restaurante_id' => $restaurante->id, 'nombre' => 'Local Test', 'estado' => 'ACTIVO']);
        $trabajador = Trabajadore::create([
            'user_id' => $user->id,
            'restaurante_id' => $restaurante->id,
            'local_id' => $local->id,
            'rol' => 'COCINA',
            'puesto' => 'Cocina',
            'activo' => true,
        ]);

        $registradoAntiguo = $this->crearPedido($local->id, $trabajador->id, 'OLD', 'REGISTRADO', now()->subMinutes(20));
        $this->crearPedido($local->id, $trabajador->id, 'PREP', 'PREPARANDO', now()->subMinutes(10));
        $this->crearPedido($local->id, $trabajador->id, 'NEW', 'REGISTRADO', now());

        $this->actingAs($user)->get('/cocina')
            ->assertOk()
            ->assertSeeInOrder(['#PREP', '#OLD', '#NEW']);

        $this->patch("/cocina/{$registradoAntiguo->id}/estado", ['estado' => 'LISTO'])->assertUnprocessable();
        $this->patch("/cocina/{$registradoAntiguo->id}/estado", ['estado' => 'PREPARANDO'])->assertRedirect('/cocina');
        $this->patch("/cocina/{$registradoAntiguo->id}/estado", ['estado' => 'LISTO'])->assertRedirect('/cocina');

        $this->assertDatabaseHas('pedidos', ['id' => $registradoAntiguo->id, 'estado' => 'LISTO']);
        $this->assertDatabaseHas('historial_estados', [
            'pedido_id' => $registradoAntiguo->id,
            'estado_anterior' => 'PREPARANDO',
            'estado_nuevo' => 'LISTO',
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
