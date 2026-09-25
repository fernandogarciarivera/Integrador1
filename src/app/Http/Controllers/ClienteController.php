<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /** Autocomplete: busca por nombre o teléfono. */
    public function buscar(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if (strlen($q) < 2) return response()->json([]);

        return Cliente::query()
            ->where(fn($w) => $w->where('nombre', 'like', "%{$q}%")
                ->orWhere('telefono', 'like', "%{$q}%"))
            ->limit(10)
            ->get(['id', 'nombre', 'telefono']);
    }
}
