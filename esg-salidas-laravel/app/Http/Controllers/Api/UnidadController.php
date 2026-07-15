<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Service\ContpaqService;
use RuntimeException;

class UnidadController extends Controller
{
    public function index(ContpaqService $contpaqService)
    {
        try {
            return response()->json([
                'data' => $contpaqService->unidadesVehiculares(),
            ]);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => 'No fue posible cargar unidades vehiculares desde CONTPAQi.',
                'error_type' => 'unidades_vehiculares_unavailable',
                'detail' => $exception->getMessage(),
            ], 503);
        }
    }
}
