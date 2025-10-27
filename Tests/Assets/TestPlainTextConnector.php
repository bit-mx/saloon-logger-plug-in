<?php

namespace BitMx\SaloonLoggerPlugIn\Tests\Assets;

use BitMx\SaloonLoggerPlugIn\Traits\HasLogging;
use Saloon\Http\Connector;

class TestPlainTextConnector extends Connector
{
    use HasLogging;

    protected int $connectTimeout = 1;

    protected int $requestTimeout = 1;

    public function resolveBaseUrl(): string
    {
        return 'https://google.com';
    }
}
