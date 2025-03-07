<?php

namespace Tmt\TmtLat\Jobs;

use Illuminate\Support\Facades\DB;

class DatabaseJob extends BaseJob
{
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->onQueue = config('tmt-lat.queue', 'tmt');
    }

    public function handle()
    {

    }


    public function shouldProcessTable($table)
    {
        return !in_array($table, config('tmt-lat.ignored_tables'));
    }
}
