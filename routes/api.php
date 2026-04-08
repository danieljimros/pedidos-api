<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PedidoController;

Route::middleware('api')->group(function () {
    // Rutas de la API - Recurso Pedidos
    Route::apiResource('pedidos', PedidoController::class);
});
