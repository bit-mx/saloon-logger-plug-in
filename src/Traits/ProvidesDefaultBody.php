<?php

namespace BitMx\SaloonLoggerPlugIn\Traits;

use Saloon\Http\Request;

/**
 * @template T of Request
 *
 * @mixin T
 */
trait ProvidesDefaultBody
{
    public function getDefaultBody(): array
    {
        return $this->defaultBody();
    }
}
