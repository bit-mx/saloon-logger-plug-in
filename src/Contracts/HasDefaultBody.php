<?php

namespace BitMx\SaloonLoggerPlugIn\Contracts;

interface HasDefaultBody
{
    /** @return array<string, mixed> */
    public function getDefaultBody(): array;
}
