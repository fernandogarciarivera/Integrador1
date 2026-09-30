<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RestauranteController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\CajaController;
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
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // restaurantes
    Route::resource('restaurantes', RestauranteController::class)
        ->except(['show'])
        ->parameters(['restaurantes' => 'restaurante']);

    Route::resource('locales', LocaleController::class)->except(['show'])->parameters(['locales' => 'local']);
    Route::post('locales/{local}/regenerar-token', [LocaleController::class, 'regenerarToken'])->name('locales.regenerar-token');

    // trabajadores
    Route::resource('trabajadores', TrabajadorController::class)
        ->except(['show'])
        ->parameters(['trabajadores' => 'trabajador']);

    // routes/web.php — descomentar (el formulario la llama por fetch y hoy recibe 404)
    Route::get('restaurantes/{restaurante}/locales', [TrabajadorController::class, 'locales'])->name('restaurantes.locales');

    Route::post('trabajadores/{trabajador}/reset-password', [TrabajadorController::class, 'resetPassword'])->name('trabajadores.reset-password');
    Route::post('trabajadores/{trabajador}/toggle-activo', [TrabajadorController::class, 'toggleActivo'])->name('trabajadores.toggle-activo');

    // Caja / Pedidos
    Route::get('caja', [CajaController::class, 'index'])->name('caja.index');
    Route::post('caja', [CajaController::class, 'store'])->name('caja.store');
    Route::get('caja/{pedido}/detalle', [CajaController::class, 'show'])->name('caja.show');
    Route::match(['patch', 'post'], 'caja/{pedido}/estado', [CajaController::class, 'cambiarEstado'])->name('caja.estado');

    Route::resource('clientes', ClienteController::class)
        ->except(['show'])
        ->parameters(['clientes' => 'cliente']);

    // Autocomplete clientes
    Route::get('clientes/buscar', [ClienteController::class, 'buscar'])->name('clientes.buscar');
});

require __DIR__ . '/auth.php';
