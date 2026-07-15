<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetalleFolio;
use App\Models\Folio;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function resumen(Request $request)
    {
        $folios = Folio::query()
            ->when($request->filled('fecha_inicio'), fn ($query) => $query->whereDate('fecha', '>=', $request->fecha_inicio))
            ->when($request->filled('fecha_fin'), fn ($query) => $query->whereDate('fecha', '<=', $request->fecha_fin))
            ->when($request->filled('departamento'), fn ($query) => $query->where('departamento_nombre', $request->departamento))
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->estado));

        $folioIds = (clone $folios)->pluck('id');

        return response()->json([
            'data' => [
                'salidas_totales' => (clone $folios)->count(),
                'productos_entregados' => DetalleFolio::query()->whereIn('folio_id', $folioIds)->sum('cantidad'),
                'salidas_hoy' => (clone $folios)->whereDate('fecha', today())->count(),
                'salidas_mes' => (clone $folios)->whereYear('fecha', now()->year)->whereMonth('fecha', now()->month)->count(),
                'salidas_sincronizadas' => (clone $folios)->where('estado', 'Sincronizado')->count(),
                'salidas_error' => (clone $folios)->where('estado', 'Error')->count(),
                'productos_mas_utilizados' => DetalleFolio::query()
                    ->selectRaw('codigo_producto, nombre_producto, SUM(cantidad) as cantidad_total')
                    ->whereIn('folio_id', $folioIds)
                    ->groupBy('codigo_producto', 'nombre_producto')
                    ->orderByDesc('cantidad_total')
                    ->limit(10)
                    ->get(),
            ],
        ]);
    }
}
