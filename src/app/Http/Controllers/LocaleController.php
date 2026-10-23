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
            ->delUsuario()
            ->when(request('local'),      fn($q, $v) => $q->where('nombre', 'like', "%{$v}%"))
            ->when(request('restaurante_id'), fn($q, $v) => $q->where('restaurante_id', $v))
            ->when(request('estado'),     fn($q, $v) => $q->where('estado', $v))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $restaurantes = Restaurante::delUsuario(['id' => 'restaurante_id'])
            ->orderBy('nombre')->get(['id', 'nombre']);

        return view('locales.index', compact('locales', 'restaurantes'));
    }

    public function create()
    {
        return view('locales.form', [
            'local'        => new Locale(['estado' => 'ACTIVO']),
            'restaurantes' => Restaurante::delUsuario(['id' => 'restaurante_id'])
                ->orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function edit(Locale $local)
    {
        $local = Locale::delUsuario()->findOrFail($local->id);

        return view('locales.form', [
            'local'        => $local,
            'restaurantes' => Restaurante::delUsuario(['id' => 'restaurante_id'])
                ->orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function store(Request $request, TrabajadorController $trabajadorController)
    {
        $data = $this->validar($request);
        $this->assertRestauranteAccesible($data['restaurante_id']);

        $local = Locale::create($data);
        // 1. Generar Token API
        $local->update(['api_token' => \Illuminate\Support\Str::random(64)]);
        // 2. Trabajador POS1 (reutiliza lógica de TrabajadorController)
        $trabajadorController->crearUsuario0ParaLocal($local);

        return redirect()->route('locales.index')->with('status', 'Local creado.');
    }

    public function update(Request $request, Locale $local)
    {
        $local = Locale::delUsuario()->findOrFail($local->id);
        $data = $this->validar($request, $local->id);
        $this->assertRestauranteAccesible($data['restaurante_id']);
        $local->update($data);

        return redirect()->route('locales.index')->with('status', 'Local actualizado.');
    }

    public function destroy(Locale $local)
    {
        $local = Locale::delUsuario()->findOrFail($local->id);
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

    private function assertRestauranteAccesible(int $restauranteId): void
    {
        abort_unless(
            Restaurante::delUsuario(['id' => 'restaurante_id'])->whereKey($restauranteId)->exists(),
            403
        );
    }

    public function regenerarToken(Locale $local)
    {
        $local = Locale::delUsuario()->findOrFail($local->id);
        $local->update(['api_token' => \Illuminate\Support\Str::random(64)]);
        return back()->with('status', "Token regenerado para {$local->nombre}.");
    }
}
