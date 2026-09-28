<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::query()
            ->when(request('cliente'),  fn($q, $v) => $q->where(fn($w) => $w
                ->where('nombre', 'like', "%{$v}%")
                ->orWhere('telefono', 'like', "%{$v}%")
                ->orWhere('email', 'like', "%{$v}%")))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.form', ['cliente' => new Cliente]);
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.form', compact('cliente'));
    }

    public function store(Request $request)
    {
        Cliente::create($this->validar($request));

        return redirect()->route('clientes.index')->with('status', 'Cliente creado.');
    }

    public function update(Request $request, Cliente $cliente)
    {
        $cliente->update($this->validar($request, $cliente->id));

        return redirect()->route('clientes.index')->with('status', 'Cliente actualizado.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')->with('status', 'Cliente eliminado.');
    }

    private function validar(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nombre'   => ['nullable', 'string', 'max:100'],
            'telefono' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('clientes', 'telefono')->ignore($ignoreId)
            ],
            'email'    => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('clientes', 'email')->ignore($ignoreId)
            ],
            'preferencias_notificacion'   => ['nullable', 'array'],
            'preferencias_notificacion.*' => ['in:SONIDO,VIBRACION,VISUAL,PUSH'],
        ]);
    }

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
