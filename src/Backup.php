<?php

namespace BitMx\SaloonLoggerPlugIn;

use BitMx\SaloonLoggerPlugIn\Contracts\BackupTransformerContract;
use BitMx\SaloonLoggerPlugIn\Models\SaloonLogger;
use Illuminate\Support\LazyCollection;

class Backup
{
    /**
     * @param  LazyCollection<int, SaloonLogger>  $logs
     */
    public function handle(
        LazyCollection $logs,
        string $name,
        string $disk,
    ): void {

        $backupTransformers = config('saloon-logger.prune.backup.transformers', []);

        if (count($backupTransformers) === 0) {
            return;
        }
        foreach ($backupTransformers as $class) {

            if (! class_exists($class) || ! is_a($class, BackupTransformerContract::class, true)) {
                continue;
            }

            $transformer = new $class;
            $transformer->handle($logs, $name, $disk);
        }

    }
}
