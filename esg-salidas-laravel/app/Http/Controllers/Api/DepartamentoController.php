<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Service\ContpaqService;
use RuntimeException;

class DepartamentoController extends Controller
{
    public function index(ContpaqService $contpaqService)
    {
        try {
            return response()->json([
                'data' => $contpaqService->clientesInternos(),
            ]);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => 'No fue posible cargar departamentos desde clientes/proveedores CONTPAQi.',
                'error_type' => 'clientes_internos_unavailable',
                'detail' => $exception->getMessage(),
            ], 503);
        }
    }
}
