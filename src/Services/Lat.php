<?php

namespace Tmt\TmtLat\Services;

use Dflydev\DotAccessData\Data;
use Illuminate\Support\Facades\Queue;
use Tmt\TmtLat\Jobs\DatabaseJob;
use Tmt\TmtLat\Jobs\FileJob;
use Tmt\TmtLat\Jobs\ArtisanJob;
use Tmt\TmtLat\Jobs\RouteJob;
use Tmt\TmtLat\Jobs\FileScanner;

class Lat
{
    private $onQueue;

    public function __construct()
    {
        $getQueue = config('tmt-lat.queue', 'tmt');
        if (isset($getQueue['name'])) {
            $this->onQueue = $getQueue['name'];
        }
    }

    public function start()
    {

        if (config('tmt-lat.collect.database')) {
            ArtisanJob::dispatch([
                'type' => 'artisan',
                'timestamp' => now(),
            ])->onQueue($this->onQueue);
        }

        if (config('tmt-lat.collect.artisan')) {
            ArtisanJob::dispatch([
                'type' => 'artisan',
                'timestamp' => now(),
            ])->onQueue($this->onQueue);
        }

        if (config('tmt-lat.collect.routes')) {
            RouteJob::dispatch([
                'type' => 'route',
                'timestamp' => now(),
            ])->onQueue($this->onQueue);
        }

        if (config('tmt-lat.collect.files')) {
            FileScanner::dispatch()->onQueue($this->onQueue);
        }
    }

    public function shouldProcessTable($table)
    {
        return !in_array($table, config('tmt-lat.ignored_tables'));
    }
}
