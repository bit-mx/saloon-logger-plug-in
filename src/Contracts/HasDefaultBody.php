<?php

namespace BitMx\SaloonLoggerPlugIn\Contracts;

interface HasDefaultBody
{
    /** @return string|array<string, mixed> */
    public function getDefaultBody(): string|array;
}
