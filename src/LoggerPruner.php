<?php

namespace BitMx\SaloonLoggerPlugIn;

use BitMx\SaloonLoggerPlugIn\Models\SaloonLogger;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class LoggerPruner
{
    private bool $isPrunable;

    private bool $isBackupable;

    private string $field;

    private string $fieldComparison;

    private Closure $fieldValue;

    private string $prefix;

    private string $suffix;

    private string $disk;

    public function __construct()
    {
        $this->isPrunable = config('saloon-logger.prune.active', false);
        $this->isBackupable = config('saloon-logger.prune.backup.active', false);
        $this->field = config('saloon-logger.prune.field');
        $this->fieldComparison = config('saloon-logger.prune.field_comparison');
        $this->fieldValue = config('saloon-logger.prune.field_value');
        $this->prefix = config('saloon-logger.prune.prefix', '');
        $this->suffix = config('saloon-logger.prune.suffix', '');
        $this->disk = config('saloon-logger.storage.disk', 'local');
    }

    /**
     * @return Builder<SaloonLogger>
     */
    public function getPruneCondition(): Builder
    {
        if (! $this->isPrunable) {
            return SaloonLogger::query()->whereRaw('1 = 0');
        }
        $closure = $this->fieldValue;

        return SaloonLogger::query()->where(
            $this->field,
            $this->fieldComparison,
            $closure()
        );
    }

    public function pruning(string $name): void
    {
        if (! $this->isPrunable) {
            return;
        }
        if (! $this->isBackupable) {
            return;
        }

        $closure = $this->fieldValue;
        $registersChunk = SaloonLogger::query()->where(
            $this->field,
            $this->fieldComparison,
            $closure()
        )->lazy(500);

        if ($registersChunk->isEmpty()) {
            return;
        }

        $name = $this->prefix.$name.$this->suffix;

        $backup = new Backup;
        $backup->handle(
            $registersChunk,
            $name,
            $this->disk
        );
    }
}
