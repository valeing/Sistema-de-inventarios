<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

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
        // Habilitar estilos de Bootstrap 5 para la paginación
        Paginator::useBootstrapFive();

        // Definir un prefijo dinámico basado en el rol del usuario autenticado
        app()->singleton('prefix', function () {
            return Auth::check() ? (Auth::user()->role->name === 'Administrador' ? 'admin' : 'operador') : null;
        });

        // Hacer que esté disponible en todas las vistas
        View::composer('*', function ($view) {
            $view->with('prefix', app('prefix'));
        });
    }
}
