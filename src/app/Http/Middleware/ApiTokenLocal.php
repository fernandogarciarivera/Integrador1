<?php

namespace App\Http\Middleware;

use App\Models\Locale;
use Closure;
use Illuminate\Http\Request;

class ApiTokenLocal
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken() ?: $request->header('X-Api-Token');

        abort_unless($token, 401, 'Token requerido.');

        $local = Locale::where('api_token', $token)->first();

        abort_unless($local, 401, 'Token inválido.');

        $request->attributes->set('api_local', $local);

        return $next($request);
    }
}
