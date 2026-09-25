<?php

// app/Http/Controllers/TrabajadorController.php
namespace App\Http\Controllers;

use App\Models\{User, Trabajador, Restaurante, Locale, PerfilAcceso};
use App\Support\ImageCropper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Hash, Storage};

class TrabajadorController extends Controller
{
    public function index()
    {
        $trabajador = auth()->user()?->trabajador;
        $trabajadores = Trabajador::with('user', 'restaurante', 'local')
            ->delUsuario() // adicionado para poder ver solo regstros de trabajadores que tienen usuario en su local y restaurante asignado, si el usuario es superadmin no filtra nada
            ->when(request('trabajador'), fn($query, $value) => $query->whereHas('user', fn($user) => $user->where('name', 'like', "%{$value}%")->orWhere('email', 'like', "%{$value}%")))
            // ->when(request('restaurante_id'), fn($query, $value) => $query->where('restaurante_id', $value)) ->when(request('local_id'), fn($query, $value) => $query->where('local_id', $value))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();
        $restaurantes = Restaurante::when($trabajador?->restaurante_id !== null, fn($query) => $query->whereKey($trabajador->restaurante_id))
            ->orderBy('nombre')->get(['id', 'nombre']);
        $roles        = PerfilAcceso::orderBy('id')->pluck('perfil');
        $locales = Locale::when($trabajador?->restaurante_id !== null, fn($query) => $query->where('restaurante_id', $trabajador->restaurante_id))
            ->when($trabajador?->local_id !== null, fn($query) => $query->whereKey($trabajador->local_id))
            ->orderBy('nombre')->get(['id', 'nombre', 'restaurante_id']);
        return view('trabajadores.index', compact('trabajadores', 'restaurantes', 'roles', 'locales'));
    }

    public function create()
    {
        return view('trabajadores.form', [
            'trabajador' => new Trabajador,
            'restaurantes' => Restaurante::delUsuario()->orderBy('nombre')->get(['id', 'nombre']),
            'locales' => collect(),
            'perfiles' => PerfilAcceso::orderBy('id')->get(['perfil']),
        ]);
    }

    public function edit(int $trabajador)
    {
        $trabajador = Trabajador::with('user')->findOrFail($trabajador);
        return view('trabajadores.form', [
            'trabajador' => $trabajador,
            'restaurantes' => Restaurante::elUsuario()->orderBy('nombre')->get(['id', 'nombre']),
            'locales' => Locale::where('restaurante_id', $trabajador->restaurante_id)->orderBy('nombre')->get(['id', 'nombre']),
            'perfiles' => PerfilAcceso::orderBy('id')->get(['perfil']),
        ]);
    }

    public function locales(Restaurante $restaurante)
    {
        return $restaurante->locales()->orderBy('nombre')->get(['id', 'nombre']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'restaurante_id' => 'required|exists:restaurantes,id',
            'local_id'       => 'nullable|exists:locales,id',
            'rol'            => 'required|exists:perfilAccesos,perfil',
            'puesto'         => 'nullable|string|max:100',
            'telefono'       => 'nullable|string|max:20',
            'imagen'         => 'nullable|image|max:2048',
            'crop_x'        => 'nullable|numeric|min:0',
            'crop_y'        => 'nullable|numeric|min:0',
            'crop_width'    => 'nullable|numeric|min:0',
            'crop_height'   => 'nullable|numeric|min:0',
        ]);

        $data['imagen_url'] = $request->hasFile('imagen')
            ? ImageCropper::cropAndStore($request->file('imagen'), $request->only(['crop_x', 'crop_y', 'crop_width', 'crop_height']), 'public', 'trabajadores')
            : null;

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name'                 => $data['name'],
                'email'                => $data['email'],
                'password'             => Hash::make('12345678'),
                'must_change_password' => true,
                'email_verified_at'    => now(),
            ]);

            Trabajador::create([
                'user_id'        => $user->id,
                'restaurante_id' => $data['restaurante_id'],
                'local_id'       => $data['local_id'] ?? null,
                'rol'            => $data['rol'],
                'puesto'         => $data['puesto'] ?? null,
                'telefono'       => $data['telefono'] ?? null,
                'activo'         => true,
                'imagen_url'     => $data['imagen_url'],
            ]);
        });

        return back()->with('status', 'Trabajador creado. Contraseña inicial: 12345678');
    }

    public function update(Request $request, Trabajador $trabajador)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $trabajador->user_id,
            'restaurante_id' => 'required|exists:restaurantes,id',
            'local_id'       => 'nullable|exists:locales,id',
            'rol'            => 'required|exists:perfilAccesos,perfil',
            'puesto'         => 'nullable|string|max:100',
            'telefono'       => 'nullable|string|max:20',
            'imagen'         => 'nullable|image|max:2048',
            'crop_x'        => 'nullable|numeric|min:0',
            'crop_y'        => 'nullable|numeric|min:0',
            'crop_width'    => 'nullable|numeric|min:0',
            'crop_height'   => 'nullable|numeric|min:0',
        ]);

        unset($data['imagen']);
        if ($request->hasFile('imagen')) {
            if ($trabajador->imagen_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $trabajador->imagen_url));
            }
            $data['imagen_url'] = ImageCropper::cropAndStore($request->file('imagen'), $request->only(['crop_x', 'crop_y', 'crop_width', 'crop_height']), 'public', 'trabajadores');
        }

        DB::transaction(function () use ($trabajador, $data) {
            $trabajador->user->update(['name' => $data['name'], 'email' => $data['email']]);
            $trabajador->update($data);
        });

        return back()->with('status', 'Trabajador actualizado.');
    }

    public function resetPassword(Trabajador $trabajador)
    {
        $trabajador->user->update([
            'password'             => Hash::make('12345678'),
            'must_change_password' => true,
        ]);
        return back()->with('status', 'Contraseña reiniciada a 12345678.');
    }

    public function toggleActivo(Trabajador $trabajador)
    {
        DB::transaction(function () use ($trabajador) {
            $trabajador->update(['activo' => !$trabajador->activo]);
            $trabajador->user->update(['email_verified_at' => $trabajador->activo ? now() : null]);
        });
        return back()->with('status', $trabajador->activo ? 'Desactivado.' : 'Activado.');
    }

    public function destroy(Trabajador $trabajador)
    {
        DB::transaction(function () use ($trabajador) {
            if ($trabajador->imagen_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $trabajador->imagen_url));
            }
            $trabajador->delete();
            $trabajador->user->delete();
        });
        return back()->with('status', 'Trabajador eliminado.');
    }
}
