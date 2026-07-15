<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ContpaqSyncException;
use App\Http\Requests\StoreSalidaRequest;
use App\Http\Resources\SalidaResource;
use App\Models\Folio;
use App\Services\Salidas\SalidaService;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use RuntimeException;

class SalidaController extends Controller
{
    public function index(Request $request)
    {
        $salidas = Folio::query()
            ->withCount('detalles')
            ->when($request->filled('folio'), fn ($query) => $query->where('folio', 'like', '%' . $request->folio . '%'))
            ->when($request->filled('departamento'), fn ($query) => $query->where(function ($subquery) use ($request) {
                $subquery
                    ->where('cliente_nombre', $request->departamento)
                    ->orWhere('departamento_nombre', $request->departamento);
            }))
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->estado))
            ->when($request->filled('fecha_inicio'), fn ($query) => $query->whereDate('fecha', '>=', $request->fecha_inicio))
            ->when($request->filled('fecha_fin'), fn ($query) => $query->whereDate('fecha', '<=', $request->fecha_fin))
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(15);

        return SalidaResource::collection($salidas);
    }

    public function store(StoreSalidaRequest $request, SalidaService $salidaService)
    {
        try {
            $salida = $salidaService->registrar($request->validated(), $request->user());

            return response()->json([
                'message' => 'Salida registrada correctamente.',
                'data' => (new SalidaResource($salida->load('detalles')))->resolve($request),
            ], 201);
        } catch (QueryException $exception) {
            report($exception);

            return response()->json([
                'message' => 'No fue posible guardar la salida localmente.',
                'error_type' => 'database_error',
            ], 500);
        } catch (ContpaqSyncException $exception) {
            return response()->json([
                'message' => $exception->errorType() === 'timeout_error'
                    ? 'La salida se guardo localmente, pero CONTPAQi no respondio dentro del tiempo esperado.'
                    : 'La salida se guardo localmente, pero no fue posible sincronizarla con CONTPAQi.',
                'upstream_status' => $exception->upstreamStatus(),
                'upstream_message' => $exception->upstreamMessage(),
                'error_type' => $exception->errorType(),
                'folio_local' => $exception->localFolio(),
            ], 502);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => 'La salida se guardo localmente, pero no fue posible sincronizarla con CONTPAQi.',
                'upstream_status' => null,
                'upstream_message' => $exception->getMessage(),
                'error_type' => 'internal_error',
            ], 502);
        }
    }

    public function show(string $folio)
    {
        $salida = Folio::query()
            ->with(['detalles', 'logsSincronizacion'])
            ->where('folio', $folio)
            ->firstOrFail();

        return new SalidaResource($salida);
    }
}
