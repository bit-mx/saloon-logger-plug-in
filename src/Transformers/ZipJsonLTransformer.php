<?php

namespace BitMx\SaloonLoggerPlugIn\Transformers;

use BitMx\SaloonLoggerPlugIn\Contracts\BackupTransformerContract;
use BitMx\SaloonLoggerPlugIn\Models\SaloonLogger;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use ZipArchive;

class ZipJsonLTransformer implements BackupTransformerContract
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
        $path = Storage::disk($disk)->path($fileName);

        $zipFileName = $name.'.zip';
        $zipPath = Storage::disk($disk)->path($zipFileName);

        $zip = new ZipArchive;

        $zip->open($zipPath, ZipArchive::CREATE);
        $zip->addFile($path, $fileName);
        $zip->close();

        if (Storage::disk($disk)->exists($zipFileName)) {
            Storage::disk($disk)->delete($fileName);
        }
    }
}
