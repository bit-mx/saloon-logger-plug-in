<?php

namespace Emontano\SaloonLoggerPlugIn\Tests;

use Emontano\SaloonLoggerPlugIn\ServiceProvider\SaloonLoggerPlugInServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;

#[WithMigration]
abstract class TestCase extends Orchestra
{
    use RefreshDatabase;
    use WithWorkbench;

    protected function getPackageProviders($app): array
    {
        return [
            SaloonLoggerPlugInServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {

        $app['config']->set('saloon-logger.redacted_fields', [
            'password',
            'secret',
            'key',
            'token',
            'authorization',
        ]);

    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations/');
    }
}
