<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->whenLoaded('role', fn () => [
                'id' => $this->role?->id,
                'nombre' => $this->role?->nombre,
            ]),
            'estatus' => $this->whenLoaded('estatus', fn () => [
                'id' => $this->estatus?->id,
                'nombre' => $this->estatus?->nombre,
            ]),
            'contpaq_usuario_id' => $this->contpaq_usuario_id,
            'ultimo_acceso' => $this->ultimo_acceso?->toISOString(),
        ];
    }
}
