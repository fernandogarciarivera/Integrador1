<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait FiltraPorAcceso
{
    /**
     * Aplica el filtro de acceso basado en el trabajador autenticado.
     * Si el usuario no es un trabajador (ej. Super Admin), no filtra nada.
     * Si es un trabajador, filtra por restaurante_id y local_id.
     */
    public function scopeDelUsuario(
        Builder $query,
        array $campos = ['restaurante_id', 'local_id'],
        bool $coincidirTodos = true
    ): Builder {
        // Si no hay usuario o no es trabajador, no filtramos. Esto cubre al Super Admin y usuarios globales.
        $trabajador = Auth::user()?->trabajador;

        if (! $trabajador) {
            return $query;
        }

        $filtros = [];
        foreach ($campos as $campo => $atributo) {
            if (is_int($campo)) {
                $campo = $atributo;
            }

            $valor = $trabajador->{$atributo};
            if ($valor !== null) {
                $filtros[$campo] = $valor;
            }
        }

        if ($filtros === []) {
            return $query;
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
