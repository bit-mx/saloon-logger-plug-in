<?php

namespace BitMx\SaloonLoggerPlugIn\Tests\Assets\Sanitizers;

use BitMx\SaloonLoggerPlugIn\Contracts\SanitizerRequestContract;
use BitMx\SaloonLoggerPlugIn\Sanitizers\Request\JsonSanitizerRequest;

class TestTxtSanitizerRequest implements SanitizerRequestContract
{
    public static function sanitize(mixed $data): mixed
    {
        if (! is_string($data)) {
            return $data;
        }

        $data = str_replace('d=', '', $data);

        $json = json_decode($data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $data;
        }

        return JsonSanitizerRequest::sanitize($json);

    }
}
