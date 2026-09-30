<?php

use App\Http\Controllers\Api\PosPedidoController;
use App\Http\Controllers\Api\PosAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.local')->prefix('pos')->group(function () {
    Route::post('login',  [PosAuthController::class, 'login']);
    Route::post('logout', [PosAuthController::class, 'logout']);

    Route::middleware('auth:web')->group(function () {
        Route::post('pedidos', [PosPedidoController::class, 'store']);
    });
});
