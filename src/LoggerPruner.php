<?php

namespace BitMx\SaloonLoggerPlugIn;

use BitMx\SaloonLoggerPlugIn\Models\SaloonLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class LoggerPruner
{
    /**
     * @return Builder<SaloonLogger>
     */
    public function getPruneCondition(): Builder
    {
        $isPrunable = config('saloon-logger.prune.active', false);

        if (! $isPrunable) {
            return SaloonLogger::query()->whereRaw('1 = 0');
        }
        $field = config('saloon-logger.prune.field');
        $field_value = config('saloon-logger.prune.field_value');
        $field_comparison = config('saloon-logger.prune.field_comparison');

        return SaloonLogger::query()->where($field, $field_comparison, $field_value());
    }

    public function pruning(string $name): void
    {
        $isPrunable = config('saloon-logger.prune.active', false);
        $isBackupable = config('saloon-logger.prune.backup.active', false);
        if (! $isPrunable) {
            return;
        }
        if (! $isBackupable) {
            return;
        }

        $prefix = config('saloon-logger.prune.prefix');
        $suffix = config('saloon-logger.prune.suffix');

        $registers = SaloonLogger::query()->where(
            config('saloon-logger.prune.field'),
            config('saloon-logger.prune.field_comparison'),
            config('saloon-logger.prune.field_value')()
        )->get();

        if ($registers->isEmpty()) {
            return;
        }

        $name = $prefix.$name.$suffix;
        $content = $registers->toJson(JSON_UNESCAPED_SLASHES);

        Storage::disk(name : config('saloon-logger.storage.disk'))
            ->put($name, $content);
    }
}
