<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Service\ContpaqService;
use RuntimeException;

class ProductoController extends Controller
{
    public function index(ContpaqService $contpaqService)
    {
        try {
            return ProductResource::collection($contpaqService->productos());
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => 'No fue posible obtener el catalogo de productos.'
            ], 503);
        }
    }

    public function show(ContpaqService $contpaqService, int|string $id)
    {
        try {
            return response()->json($contpaqService->producto($id));
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => 'No fue posible obtener la informacion del producto.',
            ], 503);
        }
    }

    public function existencia(ContpaqService $contpaqService, int|string $id)
    {
        try {
            return response()->json([
                'existencia' => $contpaqService->existencia($id),
            ]);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => 'No fue posible obtener la existencia del producto.',
            ], 503);
        }
    }

    public function unidadesMedida(ContpaqService $contpaqService)
    {
        try {
            return response()->json([
                'data' => $contpaqService->unidadesMedida(),
            ]);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => 'No fue posible obtener las unidades de medida.',
            ], 503);
        }
    }
}