<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Folio extends Model
{
    protected $fillable = [
        'folio',
        'fecha',
        'usuario_id',
        'usuario_nombre',
        'departamento_id',
        'departamento_codigo',
        'departamento_nombre',
        'cliente_codigo',
        'cliente_nombre',
        'unidad_id',
        'unidad_nombre',
        'referencia',
        'tipo_salida',
        'observaciones',
        'total_productos',
        'codigo_almacen',
        'contpaq_documento_id',
        'folio_contpaq',
        'estado',
        'fecha_envio',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'fecha_envio' => 'datetime',
    ];

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleFolio::class);
    }

    public function logsSincronizacion(): HasMany
    {
        return $this->hasMany(LogSincronizacion::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
