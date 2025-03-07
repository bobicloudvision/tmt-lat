<?php

namespace Tmt\TmtLat\Jobs;

class DatabaseJob extends BaseJob
{
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->queue = config('tmt-lat.queue.database', 'database-sync');
    }
} 