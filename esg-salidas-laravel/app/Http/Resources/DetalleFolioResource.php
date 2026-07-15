<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetalleFolioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'producto_id' => $this->producto_id,
            'codigo' => $this->codigo_producto,
            'nombre' => $this->nombre_producto,
            'cantidad' => $this->cantidad,
            'existencia' => $this->existencia_actual,
            'observaciones' => $this->observaciones,
        ];
    }
}
