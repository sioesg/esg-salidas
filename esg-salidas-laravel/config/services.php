<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'contpaq' => [
        'base_url' => env('CONTPAQ_API_BASE_URL', 'http://189.206.185.236/api/Mty'),
        'documentos_facturas_url' => env('CONTPAQ_DOCUMENTOS_FACTURAS_URL', 'http://189.206.185.236:5088/api/documentos/facturas'),
        'documentos_timeout' => env('CONTPAQ_DOCUMENTOS_TIMEOUT', 60),
        'clientes_proveedores_url' => env('CONTPAQ_CLIENTES_PROVEEDORES_URL', 'http://189.206.185.236:5088/api/clientes-proveedores'),
        'clientes_internos_codigos' => env('CONTPAQ_CLIENTES_INTERNOS_CODIGOS'),
        'clientes_internos_cache_ttl' => env('CONTPAQ_CLIENTES_INTERNOS_CACHE_TTL', 300),
        'unidades_url' => env('CONTPAQ_UNIDADES_URL', 'http://189.206.185.236/api/Mty/Unidad'),
        'unidades_cache_ttl' => env('CONTPAQ_UNIDADES_CACHE_TTL', 300),
        'documento_concepto' => env('CONTPAQ_DOCUMENTO_CONCEPTO', '102'),
        'documento_serie' => env('CONTPAQ_DOCUMENTO_SERIE', 'CONSUMOS'),
        'codigo_almacen' => env('CONTPAQ_CODIGO_ALMACEN', '1'),
        'codigo_agente' => env('CONTPAQ_CODIGO_AGENTE', ''),
        'timeout' => env('CONTPAQ_API_TIMEOUT', 15),
        'retry_times' => env('CONTPAQ_API_RETRY_TIMES', 2),
        'retry_sleep' => env('CONTPAQ_API_RETRY_SLEEP', 250),
    ],

];
