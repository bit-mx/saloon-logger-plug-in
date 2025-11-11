<?php

use BitMx\SaloonLoggerPlugIn\LoggerPruner;
use BitMx\SaloonLoggerPlugIn\Models\SaloonLogger;
use BitMx\SaloonLoggerPlugIn\Transformers\JsonLTransformer;
use BitMx\SaloonLoggerPlugIn\Transformers\ZipJsonLTransformer;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\travel;

function createSaloonLogger(int $number = 1)
{
    for ($i = 0; $i < $number; $i++) {
        SaloonLogger::create([
            'trace_id' => Str::ulid(),
            'method' => 'POST',
            'endpoint' => fake()->url(),
            'phase' => 'test',
        ]);
    }

}

it('prunes old log entries', function () {

    config(['saloon-logger.prune.active' => true]);
    config(['saloon-logger.prune.backup.active' => true]);
    createSaloonLogger(3);
    travel(1)->month();
    assertDatabaseCount(SaloonLogger::class, 3);
    createSaloonLogger();
    assertDatabaseCount(SaloonLogger::class, 4);

    $prune = (new LoggerPruner)->getPruneCondition();
    $prune->delete();

    assertDatabaseCount(SaloonLogger::class, 1);
});

it('prunes old log entries and backup', function () {
    Storage::fake('local');
    $filename = 'test';
    config(['saloon-logger.prune.active' => true]);
    config(['saloon-logger.prune.backup.active' => true]);
    config(['saloon-logger.prune.backup.prefix_file_name' => 'test']);
    config(['saloon-logger.prune.backup.transformers' => [
        JsonLTransformer::class,
        ZipJsonLTransformer::class,
    ]]);
    createSaloonLogger(3);
    travel(1)->month();
    createSaloonLogger();
    assertDatabaseCount(SaloonLogger::class, 4);

    (new LoggerPruner)->pruning($filename);
    $fullFilename = $filename.'.zip';
    Storage::disk('local')->assertExists($fullFilename);

});
