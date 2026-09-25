<?php

namespace Tests\Feature;

use App\Models\Trabajadore;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class FiltraPorAccesoTest extends TestCase
{
    public function test_filtra_por_restaurante_y_omite_local_nulo(): void
    {
        $user = new User;
        $user->setRelation('trabajador', new Trabajadore([
            'restaurante_id' => 12,
            'local_id' => null,
        ]));
        Auth::shouldReceive('user')->once()->andReturn($user);

        $query = Trabajadore::query()->delUsuario();

        $this->assertStringContainsString('restaurante_id', $query->toSql());
        $this->assertStringNotContainsString('local_id', $query->toSql());
        $this->assertSame([12], $query->getBindings());
    }
}
