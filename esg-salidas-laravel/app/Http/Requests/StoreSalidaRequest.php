<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalidaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'usuario_nombre' => ['required', 'string', 'max:255'],
            'cliente_codigo' => ['required', 'string', 'max:50'],
            'cliente_nombre' => ['required', 'string', 'max:255'],
            'referencia' => ['nullable', 'string', 'max:255'],
            'unidad_vehicular' => ['nullable', 'integer', 'gt:0'],
            'observaciones' => ['nullable', 'string'],
            'productos' => ['required', 'array', 'min:1'],
            'productos.*.id' => ['required'],
            'productos.*.codigo' => ['required', 'string', 'max:100'],
            'productos.*.nombre' => ['required', 'string', 'max:255'],
            'productos.*.cantidad' => ['required', 'numeric', 'gt:0'],
            'productos.*.existencia' => ['nullable', 'numeric'],
            'productos.*.observaciones' => ['nullable', 'string'],
        ];
    }
}
