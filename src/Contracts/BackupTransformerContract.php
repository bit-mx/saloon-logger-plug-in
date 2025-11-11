<?php

namespace BitMx\SaloonLoggerPlugIn\Contracts;

use BitMx\SaloonLoggerPlugIn\Models\SaloonLogger;
use Illuminate\Support\LazyCollection;

interface BackupTransformerContract
{
    /**
     * @param  LazyCollection<int, SaloonLogger>  $logs
     */
    public function handle(
        LazyCollection $logs,
        string $name,
        string $disk,
    ): void;
}
