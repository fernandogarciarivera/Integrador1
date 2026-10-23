<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RestauranteController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\CocinaController;
use App\Http\Controllers\DespachoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PedidoSeguimientoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('mi-pedido/{token}', [\App\Http\Controllers\PedidoSeguimientoController::class, 'show'])
    ->name('pedidos.seguimiento');

Route::post('mi-pedido/{token}/asociar-cliente', [\App\Http\Controllers\PedidoSeguimientoController::class, 'asociarCliente'])
    ->name('pedidos.asociar-cliente');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'form.access:Dashboard'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('form.access:Empresa Contrata')->group(function () {
        Route::resource('restaurantes', RestauranteController::class)
            ->except(['show'])
            ->parameters(['restaurantes' => 'restaurante']);
    });

    Route::middleware('form.access:Locales')->group(function () {
        Route::resource('locales', LocaleController::class)->except(['show'])->parameters(['locales' => 'local']);
        Route::post('locales/{local}/regenerar-token', [LocaleController::class, 'regenerarToken'])->name('locales.regenerar-token');
    });

    Route::middleware('form.access:Trabajadores')->group(function () {
        Route::resource('trabajadores', TrabajadorController::class)
            ->except(['show'])
            ->parameters(['trabajadores' => 'trabajador']);
        Route::get('restaurantes/{restaurante}/locales', [TrabajadorController::class, 'locales'])->name('restaurantes.locales');
        Route::post('trabajadores/{trabajador}/reset-password', [TrabajadorController::class, 'resetPassword'])->name('trabajadores.reset-password');
        Route::post('trabajadores/{trabajador}/toggle-activo', [TrabajadorController::class, 'toggleActivo'])->name('trabajadores.toggle-activo');
    });

    Route::middleware('form.access:Pedidos Caja')->group(function () {
        Route::get('caja', [CajaController::class, 'index'])->name('caja.index');
        Route::post('caja', [CajaController::class, 'store'])->name('caja.store');
        Route::get('caja/{pedido}/detalle', [CajaController::class, 'show'])->name('caja.show');
        Route::match(['patch', 'post'], 'caja/{pedido}/estado', [CajaController::class, 'cambiarEstado'])->name('caja.estado');
    });

    Route::middleware('form.access:Pedidos Cocina')->group(function () {
        Route::get('cocina', [CocinaController::class, 'index'])->name('cocina.index');
        Route::patch('cocina/{pedido}/estado', [CocinaController::class, 'cambiarEstado'])->name('cocina.estado');
    });

    Route::middleware('form.access:Pedidos Despacho')->group(function () {
        Route::get('despacho', [DespachoController::class, 'index'])->name('despacho.index');
        Route::patch('despacho/{pedido}/estado', [DespachoController::class, 'cambiarEstado'])->name('despacho.estado');
    });

    Route::middleware('form.access:Clientes')->group(function () {
        Route::resource('clientes', ClienteController::class)
            ->except(['show'])
            ->parameters(['clientes' => 'cliente']);
        Route::get('clientes/buscar', [ClienteController::class, 'buscar'])->name('clientes.buscar');
    });
});

require __DIR__ . '/auth.php';
