<?php

namespace App\Http\Controllers;

use App\Models\Locale;
use App\Models\Restaurante;
use App\Http\Controllers\TrabajadorController;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocaleController extends Controller
{
    public function index()
    {
        $locales = Locale::with('restaurante')
            ->when(request('local'),      fn($q, $v) => $q->where('nombre', 'like', "%{$v}%"))
            ->when(request('restaurante_id'), fn($q, $v) => $q->where('restaurante_id', $v))
            ->when(request('estado'),     fn($q, $v) => $q->where('estado', $v))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $restaurantes = Restaurante::orderBy('nombre')->get(['id', 'nombre']);

        return view('locales.index', compact('locales', 'restaurantes'));
    }

    public function create()
    {
        return view('locales.form', [
            'local'        => new Locale(['estado' => 'ACTIVO']),
            'restaurantes' => Restaurante::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function edit(Locale $local)
    {
        return view('locales.form', [
            'local'        => $local,
            'restaurantes' => Restaurante::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function store(Request $request, TrabajadorController $trabajadorController)
    {
        $local = Locale::create($this->validar($request));
        // 1. Generar Token API
        $local->update(['api_token' => \Illuminate\Support\Str::random(64)]);
        // 2. Trabajador POS1 (reutiliza lógica de TrabajadorController)
        $trabajadorController->crearUsuario0ParaLocal($local);

        return redirect()->route('locales.index')->with('status', 'Local creado.');
    }

    public function update(Request $request, Locale $local)
    {
        $local->update($this->validar($request, $local->id));

        return redirect()->route('locales.index')->with('status', 'Local actualizado.');
    }

    public function destroy(Locale $local)
    {
        $local->delete();

        return redirect()->route('locales.index')->with('status', 'Local eliminado.');
    }

    private function validar(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'restaurante_id' => ['required', 'exists:restaurantes,id'],
            'nombre'         => [
                'required',
                'string',
                'max:100',
                Rule::unique('locales', 'nombre')
                    ->where(fn($q) => $q->where('restaurante_id', $request->restaurante_id))
                    ->ignore($ignoreId)
            ],
            'direccion'      => ['nullable', 'string', 'max:200'],
            'codigo'         => ['nullable', 'string', 'max:20'],
            'estado'         => ['required', Rule::in(['ACTIVO', 'INACTIVO'])],
        ]);
    }

    public function regenerarToken(Locale $local)
    {
        $local->update(['api_token' => \Illuminate\Support\Str::random(64)]);
        return back()->with('status', "Token regenerado para {$local->nombre}.");
    }
}
