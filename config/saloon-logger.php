<?php

use BitMx\SaloonLoggerPlugIn\Sanitizers\Request\JsonSanitizerRequest;
use BitMx\SaloonLoggerPlugIn\Sanitizers\Response\JsonSanitizerResponse;
use BitMx\SaloonLoggerPlugIn\Transformers\JsonLTransformer;

return [

    /*
    |--------------------------------------------------------------------------
    | Redacted Fields
    |--------------------------------------------------------------------------
    |
    | Fields to be redacted (replaced by '***REDACTED***') before being stored in the DB.
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
    | If set to true, the plugin will automatically add an X-Trace-Id header
    | to each outgoing request
    |
    */
    'propagate_header' => true,
    'redacted_value' => '***REDACTED***',
    /*
    |--------------------------------------------------------------------------
    | Sanitizers
    |--------------------------------------------------------------------------
    |
    | List of Sanitizers to be applied to the request and response data.
    | These sanitizers need to implement the SaloonSanitizerRequest and SaloonSanitizerResponse interfaces.
    | JsonSanitizerRequest y JsonSanitizerResponse is for JSON.
    |
    */
    'sanitizers' => [
        'request' => [
            JsonSanitizerRequest::class,
        ],
        'response' => [
            JsonSanitizerResponse::class,
        ],
    ],
    'debug_mode' => env('APP_DEBUG', false),
    /*
    |--------------------------------------------------------------------------
    | Prune
    |--------------------------------------------------------------------------
    | If set to true, the plugin will automatically prune the SaloonLogger table
    | when the prune command is executed.
    | php artisan model:prune
    |
    |
    */
    'prune' => [
        // Active prune
        'active' => false,
        // Conditions to prune
        'field' => 'created_at',
        'field_value' => function () {
            return now()->subMonth();
        },
        'field_comparison' => '<=',
        /*
        |--------------------------------------------------------------------------
        | Backup before prune
        |--------------------------------------------------------------------------
        | If set to true, the plugin will automatically back up the SaloonLogger table
        |
        |
        */
        'backup' => [
            // Active backup
            'active' => false,
            // Backup disk
            'disk' => 'local',
            // Backup path
            'prefix_file_name' => 'saloon_logger',
            'suffix_file_name' => '',
            /*
            |--------------------------------------------------------------------------
            | Backup Transformers
            |--------------------------------------------------------------------------
            | The transformers to be used to determine how to back up the SaloonLogger table.
            | They need to implement the SaloonLoggerBackupTransformer interface.
            |
            */
            'transformers' => [
                JsonLTransformer::class,
                /*
                * The BitMx\SaloonLoggerPlugIn\Transformers\ZipJsonLTransformer
                * requires the zip extension to be installed.
                * This transformer needs JsonLTransformer::class
                */
                // ZipJsonLTransformer::class,
            ],
        ],
    ],

];
