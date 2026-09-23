<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrabajadorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Resource routes for trabajadores (no leading slash, explicit controller namespace)
    Route::resource('trabajadores', TrabajadorController::class)
        ->except(['show'])
        ->parameters(['trabajadores' => 'trabajador']);
    Route::get('restaurantes/{restaurante}/locales', [TrabajadorController::class, 'locales'])->name('restaurantes.locales');
    Route::post('trabajadores/{trabajador}/reset-password', [TrabajadorController::class, 'resetPassword'])->name('trabajadores.reset-password');
    Route::post('trabajadores/{trabajador}/toggle-activo', [TrabajadorController::class, 'toggleActivo'])->name('trabajadores.toggle-activo');
});

require __DIR__ . '/auth.php';
