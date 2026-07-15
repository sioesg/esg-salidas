<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalidaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'folio' => $this->folio,
            'folio_local' => $this->folio,
            'folio_contpaq' => $this->folio_contpaq,
            'contpaq_documento_id' => $this->contpaq_documento_id,
            'fecha' => $this->fecha?->format('Y-m-d H:i:s'),
            'usuario' => $this->usuario_nombre,
            'usuario_nombre' => $this->usuario_nombre,
            'registrado_por_id' => $this->usuario_id,
            'departamento' => $this->cliente_nombre ?: $this->departamento_nombre,
            'departamento_codigo' => $this->cliente_codigo ?: $this->departamento_codigo,
            'cliente_codigo' => $this->cliente_codigo ?: $this->departamento_codigo,
            'cliente_nombre' => $this->cliente_nombre ?: $this->departamento_nombre,
            'referencia' => $this->referencia ?? '',
            'unidad_vehicular' => $this->unidad_id,
            'unidad_vehicular_nombre' => $this->unidad_nombre,
            'unidad' => $this->referencia ?: '-',
            'estado' => $this->estado,
            'observaciones' => $this->observaciones,
            'total_productos' => $this->total_productos,
            'productos' => $this->relationLoaded('detalles')
                ? DetalleFolioResource::collection($this->detalles)
                : [],
        ];
    }
}
