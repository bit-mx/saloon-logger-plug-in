<?php

namespace BitMx\SaloonLoggerPlugIn\Sanitizers\Response;

use BitMx\SaloonLoggerPlugIn\Contracts\SanitizerResponseContract;
use Illuminate\Support\Str;
use JsonException;
use Saloon\Http\Response;

class JsonSanitizerResponse implements SanitizerResponseContract
{
    public static function sanitize(Response $response): mixed
    {

        try {
            if ($response->json()) {
                return $response->json();
            }
        } catch (JsonException) {
        }

        return Str::limit($response->body(), 5000);
    }
}
