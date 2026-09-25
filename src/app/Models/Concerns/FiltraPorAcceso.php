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
    public function scopeDelUsuario(Builder $query): Builder
    {
        // Si no hay usuario o no es trabajador, no filtramos. Esto cubre al Super Admin y usuarios globales.
        $trabajador = Auth::user()?->trabajador;

        if (! $trabajador) {
            return $query;
        }

        return $query
            ->when($trabajador->restaurante_id !== null, fn($q) => $q->where('restaurante_id', $trabajador->restaurante_id))
            ->when($trabajador->local_id !== null, fn($q) => $q->where('local_id', $trabajador->local_id));
    }
}
