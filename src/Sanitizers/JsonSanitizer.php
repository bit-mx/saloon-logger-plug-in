<?php

namespace BitMx\SaloonLoggerPlugIn\Sanitizers;

use BitMx\SaloonLoggerPlugIn\Contracts\SanitizerContract;
use Illuminate\Support\Str;

class JsonSanitizer implements SanitizerContract
{
    public static function sanitize(mixed $data): mixed
    {

        if (is_string($data) && Str::isJson($data)) {
            $data = json_decode($data, true);
        }

        if (! is_array($data)) {
            return $data;
        }

        /** @var string $redactedValue */
        $redactedValue = config('saloon-logger.redacted_value', '***REDACTED***');
        /** @var array<int,string> $sensitiveFields */
        $sensitiveFields = config('saloon-logger.redacted_fields', []);

        $sensitiveFields = array_map(
            fn (string $f): string => Str::lower($f),
            $sensitiveFields
        );

        if (empty($sensitiveFields)) {
            return $data;
        }

        foreach ($data as $key => $value) {

            $lowerKey = Str::lower($key);
            if (is_array($value)) {
                $data[$key] = self::sanitize($value);
            } elseif (in_array($lowerKey, $sensitiveFields)) {
                $data[$key] = $redactedValue;
            }
        }

        return $data;
    }
}
