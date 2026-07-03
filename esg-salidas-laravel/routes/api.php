<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\SalidaController;
use App\Http\Controllers\Api\DepartamentoController;
use App\Http\Controllers\Api\UnidadController;

/*
|--------------------------------------------------------------------------
| API Sistema de Salidas
|--------------------------------------------------------------------------
*/

Route::prefix('productos')->group(function () {

    // Catálogo de productos
    Route::get('/', [ProductoController::class, 'index']);

    // Existencia de un producto
    Route::get('/{id}/existencia', [ProductoController::class, 'existencia']);

    // Unidades de medida
    Route::get('/unidades-medida', [ProductoController::class, 'unidadesMedida']);

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