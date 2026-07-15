<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogSincronizacion extends Model
{
    protected $table = 'logs_sincronizacion';

    protected $fillable = [
        'folio_id',
        'intento',
        'estatus',
        'codigo_respuesta',
        'mensaje',
        'payload',
        'respuesta',
        'fecha_envio',
    ];

    protected $casts = [
        'payload' => 'array',
        'respuesta' => 'array',
        'fecha_envio' => 'datetime',
    ];

    public function folio(): BelongsTo
    {
        return $this->belongsTo(Folio::class);
    }
}
