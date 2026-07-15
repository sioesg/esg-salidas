<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = is_array($this->resource) ? $this->resource : [];

        return [
            'id' => $this->value($product, ['cidproducto', 'id', 'ID', 'Id', 'cProducto', 'CIDPRODUCTO']),
            'codigo' => $this->value($product, ['ccodigoproducto', 'codigo', 'Codigo', 'CODIGO', 'CCODIGOPRODUCTO']),
            'nombre' => $this->value($product, ['cnombreproducto', 'nombre', 'Nombre', 'NOMBRE', 'CNOMBREPRODUCTO']),
            'descripcion' => $this->value($product, ['descripcion', 'Descripcion', 'DESCRIPCION', 'CDESCRIPCIONPRODUCTO']),
            'tipo_producto' => $this->value($product, ['ctipoproducto', 'tipo_producto', 'tipoProducto', 'CTIPOPRODUCTO']),
            'existencia' => $this->value($product, ['existencia', 'Existencia', 'EXISTENCIA']),
        ];
    }

    private function value(array $product, array $keys): mixed
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $product)) {
                return $product[$key];
            }
        }

        return null;
    }
}
