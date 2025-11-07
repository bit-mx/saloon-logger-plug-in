<?php

namespace BitMx\SaloonLoggerPlugIn\Contracts;

interface SanitizerContract
{
    public static function sanitize(mixed $data): mixed;
}
