<?php

namespace Emontano\SaloonLoggerPlugIn\Contracts;

interface HasDefaultBody
{
    /** @return array<string, mixed> */
    public function getDefaultBody(): array;
}
