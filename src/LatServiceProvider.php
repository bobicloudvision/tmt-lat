<?php

namespace Tmt\TmtLat;

use Illuminate\Support\ServiceProvider;
use Tmt\TmtLat\Services\Lat;

class LatServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/tmt-lat.php', 'tmt-lat'
        );

        try {
            $this->app->singleton(Lat::class, function ($app) {
                return new Lat();
            });
        } catch (\Throwable $e) {
            // Log::error('TMT-LAT failed to register: ' . $e->getMessage());
        }
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/tmt-lat.php' => config_path('tmt-lat.php'),
            ], 'tmt-lat');
        }

        try {
            $service = $this->app->make(Lat::class);
            $service->start();
        } catch (\Throwable $e) {
            // Log::error('TMT-LAT failed to start: ' . $e->getMessage());
        }
    }
}
