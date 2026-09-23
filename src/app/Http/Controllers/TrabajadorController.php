<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\{User, Trabajadore as Trabajador, Restaurante, Locale, PerfilAcceso};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Hash};
use Illuminate\Validation\Rule;

class TrabajadorController extends Controller
{
    public function index()
    {
        $trabajadores = Trabajador::with(['user', 'restaurante', 'local'])
            ->orderByDesc('id')->paginate(20);
        return view('trabajadores.index', compact('trabajadores'));
    }

    public function create()
    {
        return view('trabajadores.form', [
            'trabajador'   => new Trabajador(),
            'restaurantes' => Restaurante::orderBy('nombre')->get(['id', 'nombre']),
            'locales'      => Locale::orderBy('nombre')->get(['id', 'nombre', 'restaurante_id']),
            'perfiles'     => PerfilAcceso::orderBy('perfil')->get(['id', 'perfil']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);

        DB::transaction(function () use ($data, &$trabajador) {
            $user = User::create([
                'name'                 => $data['nombre_completo'],
                'email'                => $data['email'],
                'password'             => Hash::make('12345678'),
                'must_change_password' => true,
                'email_verified_at'    => now(),
            ]);

            $trabajador = Trabajador::create([
                'user_id'        => $user->id,
                'restaurante_id' => $data['restaurante_id'],
                'local_id'       => $data['local_id'],
                'rol'            => $data['rol'],
                'puesto'         => $data['puesto'] ?? null,
                'telefono'       => $data['telefono'] ?? null,
                'activo'         => true,
            ]);
        });

        return redirect()->route('trabajadores.index')
            ->with('status', "Trabajador creado. Contraseña inicial: 12345678");
    }

    public function edit(Trabajador $trabajador)
    {
        return view('trabajadores.form', [
            'trabajador'   => $trabajador->load('user'),
            'restaurantes' => Restaurante::orderBy('nombre')->get(['id', 'nombre']),
            'locales'      => Locale::orderBy('nombre')->get(['id', 'nombre', 'restaurante_id']),
            'perfiles'     => PerfilAcceso::orderBy('perfil')->get(['id', 'perfil']),
        ]);
    }

    public function update(Request $request, Trabajador $trabajador)
    {
        $data = $this->validar($request, $trabajador);

        DB::transaction(function () use ($data, $trabajador) {
            $trabajador->user->update([
                'name'  => $data['nombre_completo'],
                'email' => $data['email'],
            ]);
            $trabajador->update([
                'restaurante_id' => $data['restaurante_id'],
                'local_id'       => $data['local_id'],
                'rol'            => $data['rol'],
                'puesto'         => $data['puesto'] ?? null,
                'telefono'       => $data['telefono'] ?? null,
            ]);
        });

        return redirect()->route('trabajadores.index')->with('status', 'Trabajador actualizado.');
    }

    public function resetPassword(Trabajador $trabajador)
    {
        $trabajador->user->update([
            'password'             => Hash::make('12345678'),
            'must_change_password' => true,
        ]);
        return back()->with('status', 'Contraseña reseteada a 12345678.');
    }

    public function toggleActivo(Trabajador $trabajador)
    {
        $activo = ! $trabajador->activo;
        DB::transaction(function () use ($trabajador, $activo) {
            $trabajador->update(['activo' => $activo]);
            $trabajador->user->update(['email_verified_at' => $activo ? now() : null]);
        });
        return back()->with('status', $activo ? 'Trabajador activado.' : 'Trabajador desactivado.');
    }

    private function validar(Request $request, ?Trabajador $trabajador = null): array
    {
        return $request->validate([
            'nombre_completo' => ['required', 'string', 'max:255'],
            'email'           => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($trabajador?->user_id)
            ],
            'restaurante_id'  => ['required', 'exists:restaurantes,id'],
            'local_id'        => ['required', 'exists:locales,id'],
            'rol'             => ['required', 'exists:perfilAccesos,perfil'],
            'puesto'          => ['nullable', 'string', 'max:100'],
            'telefono'        => ['nullable', 'string', 'max:20'],
        ]);
    }
}
