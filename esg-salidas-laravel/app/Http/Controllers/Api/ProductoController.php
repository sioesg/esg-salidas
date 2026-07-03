<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ProductoController extends Controller
{
    public function index()
    {
        return response()->json([
            'mensaje' => 'Listado de productos'
        ]);
    }

    public function existencia($id)
    {
        return response()->json([
            'producto' => $id
        ]);
    }

    public function unidadesMedida()
    {
        return response()->json([
            'mensaje' => 'Unidades de medida'
        ]);
    }
}