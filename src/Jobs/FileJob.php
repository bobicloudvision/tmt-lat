<?php

namespace Tmt\TmtLat\Jobs;

class FileJob extends BaseJob
{
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->queue = config('tmt-lat.queue.files', 'file-sync');
    }
} 