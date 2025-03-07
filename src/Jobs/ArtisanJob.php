<?php

namespace Tmt\TmtLat\Jobs;

class ArtisanJob extends BaseJob
{
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->queue = config('tmt-lat.queue', 'tmt');
    }
} 