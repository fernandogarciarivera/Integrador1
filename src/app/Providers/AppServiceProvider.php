<?php

namespace App\Providers;

use App\View\Components\AppLayout;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('admin', function ($user, $class = null, $roles = []) {
            $isSuperUser = (bool) ($user->superuser ?? false)
                || (bool) ($user->is_superadmin ?? false)
                || (bool) ($user->is_super_admin ?? false);

            if ($isSuperUser) {
                return true;
            }

            $aimeosSupport = '\Aimeos\Shop\Base\Support';
            if (class_exists($aimeosSupport)) {
                return app($aimeosSupport)->checkUserGroup($user, $roles);
            }

            return false;
        });

        View::composer('layouts.app', function ($view) {
            $view->with('menuItems', AppLayout::resolveMenuItems())
                ->with('trabajador', AppLayout::resolveTrabajador())
                ->with('authUser', auth()->user());
        });
    }
}
