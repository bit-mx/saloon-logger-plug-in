<?php

namespace BitMx\SaloonLoggerPlugIn\Contracts;

use Saloon\Http\Response;

interface SanitizerResponseContract
{
    public static function sanitize(Response $response): mixed;
}
