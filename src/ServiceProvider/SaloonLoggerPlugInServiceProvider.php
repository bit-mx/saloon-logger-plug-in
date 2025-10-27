<?php

namespace Emontano\SaloonLoggerPlugIn\ServiceProvider;

use Illuminate\Support\ServiceProvider;

class SaloonLoggerPlugInServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'saloon-logger-migrations');

        $this->publishes([
            __DIR__.'/../config/saloon_logger.php' => config_path('saloon-logger.php'),
        ], 'saloon-logger-config');

    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/saloon-logger.php', 'saloon-logger'
        );
    }
}
