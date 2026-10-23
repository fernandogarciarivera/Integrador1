<?php

namespace Tests\Feature;

use App\Models\Pedido;
use App\Models\Restaurante;
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

    public function test_por_defecto_exige_todos_los_campos_disponibles(): void
    {
        $user = new User;
        $user->setRelation('trabajador', new Trabajadore([
            'restaurante_id' => 12,
            'local_id' => 7,
        ]));
        Auth::shouldReceive('user')->once()->andReturn($user);

        $query = Trabajadore::query()->delUsuario();

        $this->assertStringContainsString('`restaurante_id` = ? and `local_id` = ?', $query->toSql());
        $this->assertSame([12, 7], $query->getBindings());
    }

    public function test_admin_scope_uses_restaurant_and_ignores_assigned_local(): void
    {
        $user = new User;
        $user->setRelation('trabajador', new Trabajadore([
            'rol' => 'ADMIN',
            'restaurante_id' => 12,
            'local_id' => 7,
        ]));
        Auth::shouldReceive('user')->once()->andReturn($user);

        $query = Trabajadore::query()->delUsuario();

        $this->assertStringContainsString('`restaurante_id` = ?', $query->toSql());
        $this->assertStringNotContainsString('`local_id` = ?', $query->toSql());
        $this->assertSame([12], $query->getBindings());
    }

    public function test_super_admin_scope_is_unrestricted_by_assignment(): void
    {
        $user = new User;
        $user->setRelation('trabajador', new Trabajadore([
            'rol' => 'SUPER_ADMIN',
            'restaurante_id' => null,
            'local_id' => null,
        ]));
        Auth::shouldReceive('user')->once()->andReturn($user);

        $query = Restaurante::query()->delUsuario(['id' => 'restaurante_id']);

        $this->assertSame([], $query->getBindings());
        $this->assertStringNotContainsString('`restaurante_id` = ?', $query->toSql());
        $this->assertStringNotContainsString('`local_id` = ?', $query->toSql());
    }

    public function test_admin_order_scope_includes_all_locals_in_assigned_restaurant(): void
    {
        $query = Pedido::query()->enAlcanceDe(new Trabajadore([
            'rol' => 'ADMIN',
            'restaurante_id' => 12,
            'local_id' => 7,
        ]));

        $this->assertStringContainsString('exists', strtolower($query->toSql()));
        $this->assertSame([12], $query->getBindings());
    }

    public function test_super_admin_order_scope_has_no_restaurant_filter(): void
    {
        $query = Pedido::query()->enAlcanceDe(new Trabajadore([
            'rol' => 'SUPER_ADMIN',
            'restaurante_id' => null,
            'local_id' => null,
        ]));

        $this->assertSame([], $query->getBindings());
        $this->assertStringNotContainsString('exists', strtolower($query->toSql()));
    }

    public function test_permite_mapear_campos_y_elegir_si_coincide_cualquiera(): void
    {
        $user = new User;
        $user->setRelation('trabajador', new Trabajadore([
            'restaurante_id' => 12,
            'local_id' => 7,
        ]));
        Auth::shouldReceive('user')->once()->andReturn($user);

        $query = Restaurante::query()->delUsuario(['id' => 'restaurante_id']);

        $this->assertStringContainsString('`id` = ?', $query->toSql());
        $this->assertSame([12], $query->getBindings());
    }

    public function test_puede_coincidir_con_cualquiera_de_los_campos(): void
    {
        $user = new User;
        $user->setRelation('trabajador', new Trabajadore([
            'restaurante_id' => 12,
            'local_id' => 7,
        ]));
        Auth::shouldReceive('user')->once()->andReturn($user);

        $query = Trabajadore::query()->delUsuario(['restaurante_id', 'local_id'], false);

        $this->assertStringContainsString(' or ', $query->toSql());
        $this->assertSame([12, 7], $query->getBindings());
    }
}
