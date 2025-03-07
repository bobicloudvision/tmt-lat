<?php

namespace Tmt\TmtLat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Database\Events\MigrationsStarted;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Tmt\TmtLat\Jobs\DatabaseJob;
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

        if (config('tmt-lat.collect.database')) {

            $databaseJobDispatch = function ($info = '') {
                DatabaseJob::dispatch([
                    'type' => 'database-change',
                    'timestamp' => now(),
                    'info' => $info,
                ])->onQueue(config('tmt-lat.queue.name', 'tmt'));
            };
            Event::listen('eloquent.created: *', function ($event) use ($databaseJobDispatch) {
                $databaseJobDispatch($event);
            });
            Event::listen('eloquent.updated: *', function ($event) use ($databaseJobDispatch) {
                $databaseJobDispatch($event);
            });
            Event::listen('eloquent.deleted: *', function ($event) use ($databaseJobDispatch) {
                $databaseJobDispatch($event);
            });

            Event::listen(MigrationsStarted::class, $databaseJobDispatch);
            Event::listen(MigrationsEnded::class, $databaseJobDispatch);
        }

//        try {
//            $service = $this->app->make(Lat::class);
//            $service->start();
//        } catch (\Throwable $e) {
//            // Log::error('TMT-LAT failed to start: ' . $e->getMessage());
//        }
    }
}
