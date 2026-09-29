<?php

use App\Http\Controllers\Api\PosPedidoController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.local')->prefix('pos')->group(function () {
    Route::post('pedidos', [PosPedidoController::class, 'store'])->name('api.pos.pedidos.store');
});
