<?php

namespace Emontano\SaloonLoggerPlugIn\Traits;

/**
 * @method defaultBody()
 */
trait RequestHelper
{
    public function getDefaultBody(): array
    {
        return $this->defaultBody();
    }
}
