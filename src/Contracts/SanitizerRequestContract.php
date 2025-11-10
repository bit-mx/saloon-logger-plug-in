<?php

namespace BitMx\SaloonLoggerPlugIn\Contracts;

interface SanitizerRequestContract
{
    public static function sanitize(mixed $data): mixed;
}
