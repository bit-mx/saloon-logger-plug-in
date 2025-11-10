<?php

namespace BitMx\SaloonLoggerPlugIn\Traits;

use BitMx\SaloonLoggerPlugIn\LoggerPruner;
use BitMx\SaloonLoggerPlugIn\Models\SaloonLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Str;

trait PrunableLog
{
    use Prunable;

    /**
     * @return Builder<SaloonLogger>
     */
    public function prunable(): Builder
    {
        return (new LoggerPruner)->getPruneCondition();
    }

    /**
     * Prepare the model for pruning.
     */
    protected function pruning(): void
    {
        (new LoggerPruner)->pruning(
            name: now()->timestamp.'-'.md5(Str::random()),
        );
    }
}
