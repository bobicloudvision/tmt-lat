<?php

namespace Tmt\TmtLat\Jobs;

class RouteJob extends BaseJob
{
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->queue = config('tmt-lat.queue.routes', 'route-sync');
    }
} 