<?php

use App\Http\Controllers\ProfileController;
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
    Route::resource('/trabajadores', \App\Http\Controllers\TrabajadorController::class)->except(['show', 'destroy']);
    Route::post('/trabajadores/{trabajador}/reset-password', [\App\Http\Controllers\TrabajadorController::class, 'resetPassword'])->name('trabajadores.reset-password');
    Route::post('/trabajadores/{trabajador}/toggle-activo', [\App\Http\Controllers\TrabajadorController::class, 'toggleActivo'])->name('trabajadores.toggle-activo');
});

require __DIR__ . '/auth.php';
