<?php

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

    'debug_mode' => env('APP_DEBUG', false),

];
