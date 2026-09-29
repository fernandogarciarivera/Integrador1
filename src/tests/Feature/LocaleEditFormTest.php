<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\Restaurante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleEditFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_form_populates_restaurante_id_for_valid_submission(): void
    {
        $user = User::factory()->create();
        $restaurante = Restaurante::create([
            'nombre' => 'Empresa Demo',
            'estado' => 'ACTIVO',
            'plan' => 'BASICO',
        ]);
        $local = Locale::create([
            'restaurante_id' => $restaurante->id,
            'nombre' => 'Local Demo',
            'estado' => 'ACTIVO',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('locales.edit', $local));

        $response->assertOk();
        $response->assertSee('Empresa');
        $response->assertSee('value="' . $restaurante->id . '"', false);
        $response->assertSee('Empresa Demo', false);
    }
}
