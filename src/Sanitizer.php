<?php

namespace BitMx\SaloonLoggerPlugIn;

use BitMx\SaloonLoggerPlugIn\Contracts\SanitizerRequestContract;
use BitMx\SaloonLoggerPlugIn\Contracts\SanitizerResponseContract;
use Saloon\Http\Response;

class Sanitizer
{
    public function request(mixed $data): mixed
    {
        $sanitizers = config('saloon-logger.sanitizers.request', []);

        if (count($sanitizers) === 0) {
            return $data;
        }

        return $this->sanitizer(
            $data,
            $sanitizers,
            SanitizerRequestContract::class
        );
    }

    public function response(Response $data): mixed
    {
        $sanitizers = config('saloon-logger.sanitizers.response', []);

        if (count($sanitizers) === 0) {
            return $data;
        }

        return $this->sanitizer(
            $data,
            $sanitizers,
            SanitizerResponseContract::class
        );
    }

    /**
     * @param  array<int,string>  $sanitizers
     */
    private function sanitizer(
        mixed $data,
        array $sanitizers,
        string $contract
    ): mixed {

        foreach ($sanitizers as $class) {

            if (! class_exists($class) || ! is_a($class, $contract, true)) {
                continue;
            }

            $data = $class::sanitize($data);
        }

        return $data;
    }
}
