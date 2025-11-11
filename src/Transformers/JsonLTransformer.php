<?php

namespace BitMx\SaloonLoggerPlugIn\Transformers;

use BitMx\SaloonLoggerPlugIn\Contracts\BackupTransformerContract;
use BitMx\SaloonLoggerPlugIn\Models\SaloonLogger;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;

class JsonLTransformer implements BackupTransformerContract
{
    /**
     * @param  LazyCollection<int, SaloonLogger>  $logs
     */
    public function handle(
        LazyCollection $logs,
        string $name,
        string $disk,
    ): void {
        $fileName = $name.'.jsonl';

        Storage::disk($disk)
            ->put($fileName, '');

        $logs->chunk(500)
            ->each(function (LazyCollection $chunk) use ($fileName, $disk) {
                $chunk->each(function (SaloonLogger $register) use ($fileName, $disk) {
                    $content = $register->toJson(JSON_UNESCAPED_SLASHES).PHP_EOL;
                    Storage::disk($disk)
                        ->append(
                            $fileName,
                            $content
                        );
                });
            });
    }
}
