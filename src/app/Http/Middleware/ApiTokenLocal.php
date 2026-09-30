<?php

namespace App\Http\Middleware;

use App\Models\Locale;
use App\Models\Trabajador;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiTokenLocal
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Token del local
        $token = $request->bearerToken() ?: $request->header('X-Api-Token');
        abort_unless($token, 401, 'Token requerido.');

        $local = Locale::where('api_token', $token)->first();
        abort_unless($local, 401, 'Token inválido.');
        $request->attributes->set('api_local', $local);

        // 2. Credenciales del usuario POS
        $email = $request->input('email');
        $password = $request->input('password');
        abort_unless($email && $password, 401, 'Credenciales requeridas.');

        // 3. Validar que el trabajador exista, esté activo y tenga rol POS
        $trabajador = Trabajador::whereHas('user', fn($q) => $q->where('email', $email))
            ->where('local_id', $local->id)
            ->where('rol', 'POS')
            ->where('activo', true)
            ->with('user')
            ->first();

        abort_unless($trabajador && Hash::check($password, $trabajador->user->password), 401, 'Credenciales invaálidas.');

        $request->attributes->set('api_trabajador', $trabajador);

        return $next($request);
    }
}
