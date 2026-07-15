<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepartamentoController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\ReporteController;
use App\Http\Controllers\Api\SalidaController;
use App\Http\Controllers\Api\UnidadController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('productos')->group(function () {
        Route::get('/', [ProductoController::class, 'index']);
        Route::get('/unidades-medida', [ProductoController::class, 'unidadesMedida']);
        Route::get('/{id}', [ProductoController::class, 'show']);
        Route::get('/{id}/existencia', [ProductoController::class, 'existencia']);
    });

    Route::prefix('salidas')->group(function () {
        Route::get('/', [SalidaController::class, 'index']);
        Route::post('/', [SalidaController::class, 'store']);
        Route::get('/{folio}', [SalidaController::class, 'show']);
    });

    Route::prefix('departamentos')->group(function () {
        Route::get('/', [DepartamentoController::class, 'index']);
    });

    Route::prefix('unidades')->group(function () {
        Route::get('/', [UnidadController::class, 'index']);
    });

    Route::prefix('reportes')->group(function () {
        Route::get('/resumen', [ReporteController::class, 'resumen']);
    });
});
