<?php

namespace BitMx\SaloonLoggerPlugIn\Tests\Assets;

use BitMx\SaloonLoggerPlugIn\Traits\HasLogging;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class TestJsonConnector extends Connector
{
    use AcceptsJson, HasLogging;

    protected int $connectTimeout = 1;

    protected int $requestTimeout = 1;

    public function resolveBaseUrl(): string
    {
        return 'https://google.com';
    }
}
