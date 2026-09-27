<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\Pedido;
use App\Models\Restaurante;
use App\Models\Trabajadore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CajaEstadoPermisosTest extends TestCase
{
    use RefreshDatabase;

    public function test_caja_role_cannot_change_to_restricted_states_and_cannot_change_finalized_orders(): void
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
            'codigo_pedido' => 'PO-TEST-100',
            'codigo_qr' => '10-PO-TEST-100',
            'tipo' => 'PRESENCIAL',
            'estado' => 'REGISTRADO',
            'fecha_pedido' => now(),
            'total' => 25.00,
        ]);

        $this->actingAs($user)
            ->patchJson('/caja/' . $pedido->id . '/estado', ['estado' => 'PREPARANDO'])
            ->assertStatus(403);

        $pedido->update(['estado' => 'ENTREGADO']);

        $this->actingAs($user)
            ->patchJson('/caja/' . $pedido->id . '/estado', ['estado' => 'CANCELADO'])
            ->assertStatus(403);
    }
}
