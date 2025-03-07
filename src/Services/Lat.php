<?php

namespace Tmt\TmtLat\Services;

use Illuminate\Support\Facades\Queue;
use Tmt\TmtLat\Jobs\DatabaseJob;
use Tmt\TmtLat\Jobs\FileJob;
use Tmt\TmtLat\Jobs\ArtisanJob;
use Tmt\TmtLat\Jobs\RouteJob;
use Tmt\TmtLat\Jobs\FileScanner;

class Lat
{
    private $queue;

    public function __construct()
    {
        $this->queue = config('tmt-lat.queue', 'default');
    }

    public function start()
    {
        if (config('tmt-lat.collect.database')) {
            Queue::connection($this->queue)->push(function () {
                DatabaseJob::dispatch([
                    'type' => 'database',
                    'timestamp' => now(),
                ]);
            });
        }

        if (config('tmt-lat.collect.artisan')) {
            Queue::connection($this->queue)->push(function () {
                ArtisanJob::dispatch([
                    'type' => 'artisan',
                    'timestamp' => now(),
                ]);
            });
        }

        if (config('tmt-lat.collect.routes')) {
            Queue::connection($this->queue)->push(function () {
                RouteJob::dispatch([
                    'type' => 'route',
                    'timestamp' => now(),
                ]);
            });
        }

        if (config('tmt-lat.collect.files')) {
            Queue::connection($this->queue)->push(function () {
                FileScanner::dispatch();
            });
        }
    }

    public function shouldProcessTable($table)
    {
        return !in_array($table, config('tmt-lat.ignored_tables'));
    }
} 