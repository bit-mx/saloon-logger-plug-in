<?php

namespace BitMx\SaloonLoggerPlugIn\Models;

use Illuminate\Database\Eloquent\Model;

class SaloonLogger extends Model
{
    protected $table = 'saloon_logger';

    protected $guarded = ['id'];

    protected $casts = [
        'headers' => 'array',
        'query' => 'array',
        'payload' => 'array',
        'response' => 'array',
    ];
}
