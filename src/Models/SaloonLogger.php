<?php

namespace BitMx\SaloonLoggerPlugIn\Models;

use Illuminate\Database\Eloquent\Model;

class SaloonLogger extends Model
{
    protected $table = 'saloon_loggers';

    protected $guarded = ['id'];

    protected $fillable = [
        'trace_id',
        'phase',
        'method',
        'endpoint',
        'headers',
        'query',
        'payload',
        'status',
        'response',
    ];

    protected $casts = [
        'headers' => 'array',
        'query' => 'array',
        'payload' => 'array',
        'response' => 'array',
    ];
}
