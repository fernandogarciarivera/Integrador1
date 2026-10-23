<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AutorizaFormulario
{
    public function handle(Request $request, Closure $next, string $formulario)
    {
        $trabajador = $request->user()?->trabajador;

        abort_unless($trabajador && $trabajador->activo, 403);

        $autorizado = DB::table('perfilesFormularios as pf')
            ->join('perfilAccesos as pa', 'pa.id', '=', 'pf.perfilAcceso_id')
            ->join('formularios as f', 'f.id', '=', 'pf.formulario_id')
            ->where('pa.perfil', $trabajador->rol)
            ->where('f.formulario', $formulario)
            ->exists();

        abort_unless($autorizado, 403);

        return $next($request);
    }
}
