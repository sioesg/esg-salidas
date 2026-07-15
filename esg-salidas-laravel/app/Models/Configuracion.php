<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuracion';

    protected $fillable = [
        'nombre_empresa',
        'api_contpaq_url',
        'api_contpaq_token',
        'api_contpaq_timeout',
        'folio_prefijo',
        'ultimo_folio',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
