<?php

namespace App\View\Components;

use App\Models\Formulario;
use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public static function isSuperAdmin(User $user): bool
    {
        $flags = [
            data_get($user, 'superuser'),
            data_get($user, 'is_superadmin'),
            data_get($user, 'is_super_admin'),
            data_get($user, 'es_super_admin'),
            $user->name === 'Super Admin',
        ];

        return collect($flags)->contains(fn($value) => filter_var($value, FILTER_VALIDATE_BOOLEAN) === true);
    }

    public static function resolveTrabajador(): ?Trabajador
    {
        if (! Auth::check()) {
            return null;
        }

        return Trabajador::with(['restaurante', 'local'])
            ->where('user_id', Auth::id())
            ->first();
    }

    public static function resolveMenuItems(): \Illuminate\Support\Collection
    {
        if (! Auth::check()) {
            return collect();
        }

        $user = Auth::user();

        if (self::isSuperAdmin($user)) {
            return Formulario::query()->orderBy('formulario')->get();
        }

        $trabajador = self::resolveTrabajador();

        if (! $trabajador) {
            return collect();
        }

        return Formulario::query()
            ->whereIn('id', function ($query) use ($trabajador) {
                $query->select('pf.formulario_id')
                    ->from('perfilesFormularios as pf')
                    ->join('perfilAccesos as pa', 'pf.perfilAcceso_id', '=', 'pa.id')
                    ->where('pa.perfil', $trabajador->rol);
            })
            ->orderBy('formulario')
            ->get();
    }

    public static function resolveMenuUrl(?string $controller, ?string $label): string
    {
        $exactRoutes = [
            'Dashboard' => 'dashboard',
            'ProfileController' => 'profile.edit',

            //'CajaController' => 'caja.index',
            //'Caja' => 'caja.index',

            //'CocinaController' => 'cocina.index',
            //'Cocina' => 'cocina.index',

            //'DespachoController' => 'despacho.index',
            //'Despacho' => 'despacho.index',

            //'ReporteController' => 'reporte.index',
            //'Reporte' => 'reporte.index',

            'TrabajadoresController' => 'trabajadores.index',
            'Trabajadores' => 'trabajadores.index',

            //'RestaurantController' => 'restaurant.index',
            //'Restaurant' => 'restaurant.index',

            //'LocalesController' => 'locales.index',
            //'Locales' => 'locales.index',

            'Perfil' => 'trabajadores.index',
            'Perfiles' => 'trabajadores.index',
            'Gestion de perfiles' => 'trabajadores.index',
        ];

        foreach ([$controller, $label] as $value) {
            if (blank($value)) {
                continue;
            }

            $candidate = trim($value);
            if (isset($exactRoutes[$candidate])) {
                return route($exactRoutes[$candidate]);
            }

            $normalized = Str::of($candidate)
                ->replaceMatches('/Controller$/', '')
                ->replaceMatches('/(?<!^)[A-Z]/', '-$0')
                ->replaceMatches('/[_\s]+/', '-')
                ->trim('-')
                ->lower()
                ->toString();

            if (isset($exactRoutes[$candidate]) || $normalized === '') {
                continue;
            }

            if (Route::has($normalized)) {
                return route($normalized);
            }

            if (Route::has($normalized . '.index')) {
                return route($normalized . '.index');
            }

            if (Route::has(Str::plural($normalized))) {
                return route(Str::plural($normalized));
            }
        }

        return '#';
    }

    public function render(): View
    {
        return view('layouts.app', [
            'menuItems' => self::resolveMenuItems(),
            'trabajador' => self::resolveTrabajador(),
        ]);
    }
}
