<?php

namespace Tmt\TmtLat\Services;

use Tmt\TmtLat\Jobs\ArtisanJob;
use Tmt\TmtLat\Jobs\DatabaseJob;
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

}
