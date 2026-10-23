<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait FiltraPorAcceso
{
    /**
     * Super Admin sees all records; ADMIN sees the whole assigned restaurant;
     * other workers are restricted to their assigned restaurant and local.
     */
    public function scopeDelUsuario(
        Builder $query,
        array $campos = ['restaurante_id', 'local_id'],
        bool $coincidirTodos = true
    ): Builder {
        $user = Auth::user();

        if (! $user) {
            return $query;
        }

        $trabajador = $user->trabajador;
        if (! $trabajador) {
            return $query->whereRaw('1 = 0');
        }

        $rol = strtoupper((string) $trabajador->rol);
        if ($rol === 'SUPER_ADMIN') {
            return $query;
        }

        if ($trabajador->restaurante_id === null) {
            return $query->whereRaw('1 = 0');
        }

        $filtros = [];
        foreach ($campos as $campo => $atributo) {
            if (is_int($campo)) {
                $campo = $atributo;
            }

            if ($rol === 'ADMIN' && $atributo === 'local_id') {
                continue;
            }

            $valor = $trabajador->{$atributo};
            if ($valor !== null) {
                $filtros[$campo] = $valor;
            }
        }

        if ($filtros === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $query) use ($filtros, $coincidirTodos) {
            foreach ($filtros as $campo => $valor) {
                $coincidirTodos
                    ? $query->where($campo, $valor)
                    : $query->orWhere($campo, $valor);
            }
        });
    }
}
