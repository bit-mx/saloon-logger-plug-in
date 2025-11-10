<?php

use BitMx\SaloonLoggerPlugIn\Sanitizers\Request\JsonSanitizerRequest;
use BitMx\SaloonLoggerPlugIn\Sanitizers\Response\JsonSanitizerResponse;

return [

    /*
    |--------------------------------------------------------------------------
    | Redacted Fields
    |--------------------------------------------------------------------------
    |
    | Lista de campos (claves) de headers o payloads que deben ser censurados
    | (reemplazados por '***REDACTED***') antes de ser almacenados en la DB.
    |
    */
    'redacted_fields' => [
        'password',
        'secret',
        'key',
        'token',
        'authorization',
        'x-api-key',
    ],

    /*
    |--------------------------------------------------------------------------
    | Propagate X-Trace-Id Header
    |--------------------------------------------------------------------------
    |
    | Si se establece en true, el plugin agregará automáticamente un header
    | X-Trace-Id a cada petición saliente con el ULID generado para la traza.
    |
    */
    'propagate_header' => true,
    'redacted_value' => '***REDACTED***',
    'sanitizers' => [
        'request' => [
            JsonSanitizerRequest::class,
        ],
        'response' => [
            JsonSanitizerResponse::class,
        ],
    ],
    'debug_mode' => env('APP_DEBUG', false),

    'prune' => [
        // Active prune
        'active' => false,
        // Conditions to prune
        'field' => 'created_at',
        'field_value' => function () {
            return now()->subMonth();
        },
        'field_comparison' => '<=',
        'backup' => [
            // Active backup
            'active' => false,
            // Backup disk
            'disk' => 'local',
            // Backup path
            'prefix_file_name' => 'saloon_logger',
            'suffix_file_name' => '',
        ],
    ],

];
