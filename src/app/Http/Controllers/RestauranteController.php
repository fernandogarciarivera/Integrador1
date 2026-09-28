<?php

namespace App\Http\Controllers;

use App\Models\Restaurante;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RestauranteController extends Controller
{
    public function index()
    {
        $restaurantes = Restaurante::query()
            ->when(request('restaurante'), fn($q, $v) => $q->where('nombre', 'like', "%{$v}%"))
            ->when(request('estado'), fn($q, $v) => $q->where('estado', $v))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('restaurantes.index', compact('restaurantes'));
    }

    public function create()
    {
        return view('restaurantes.form', ['restaurante' => new Restaurante]);
    }

    public function edit(Restaurante $restaurante)
    {
        return view('restaurantes.form', compact('restaurante'));
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        Restaurante::create($data);

        return redirect()->route('restaurantes.index')
            ->with('status', 'Restaurante creado.');
    }

    public function update(Request $request, Restaurante $restaurante)
    {
        $data = $this->validar($request, $restaurante->id);
        $restaurante->update($data);

        return redirect()->route('restaurantes.index')
            ->with('status', 'Restaurante actualizado.');
    }

    public function destroy(Restaurante $restaurante)
    {
        $restaurante->delete();

        return redirect()->route('restaurantes.index')
            ->with('status', 'Restaurante eliminado.');
    }

    private function validar(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nombre'    => ['required', 'string', 'max:100', Rule::unique('restaurantes', 'nombre')->ignore($ignoreId)],
            'direccion' => ['nullable', 'string', 'max:200'],
            'telefono'  => ['nullable', 'string', 'max:20'],
            'email'     => ['nullable', 'email', 'max:100'],
            'plan'      => ['required', Rule::in(['BASICO', 'PRO', 'ENTERPRISE'])],
            'estado'    => ['required', Rule::in(['ACTIVO', 'INACTIVO'])],
        ]);
    }
}
