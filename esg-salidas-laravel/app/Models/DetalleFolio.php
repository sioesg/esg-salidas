<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleFolio extends Model
{
    protected $fillable = [
        'folio_id',
        'producto_id',
        'codigo_producto',
        'nombre_producto',
        'unidad_id',
        'unidad_medida',
        'cantidad',
        'precio',
        'existencia_actual',
        'observaciones',
    ];

    protected $casts = [
        'cantidad' => 'decimal:4',
        'precio' => 'decimal:2',
        'existencia_actual' => 'decimal:4',
    ];

    public function folio(): BelongsTo
    {
        return $this->belongsTo(Folio::class);
    }
}
