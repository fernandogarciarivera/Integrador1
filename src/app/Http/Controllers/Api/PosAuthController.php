<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trabajador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PosAuthController extends Controller
{
    public function login(Request $request)
    {
        $local = $request->attributes->get('api_local');

        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $trabajador = Trabajador::whereHas('user', fn($q) => $q->where('email', $data['email']))
            ->where('local_id', $local->id)
            ->where('rol', 'POS')
            ->where('activo', true)
            ->with('user')
            ->first();

        // Hash::check(texto_plano, hash_guardado_en_bd)  ← esto compara texto contra hash
        abort_unless(
            $trabajador && Hash::check($data['password'], $trabajador->user->password),
            401,
            'Credenciales inválidas.'
        );

        Auth::guard('web')->login($trabajador->user);
        $request->session()->regenerate();

        return response()->json(['ok' => true, 'trabajador_id' => $trabajador->id]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['ok' => true]);
    }
}
