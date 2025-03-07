<?php

namespace Tmt\TmtLat\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $queue;
    protected $tries;
    protected $timeout;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->queue = config('tmt-lat.queue', 'tmt');
        $this->tries = config('tmt-lat.queue.tries', 3);
        $this->timeout = config('tmt-lat.queue.timeout', 30);
    }

    public function handle()
    {
        ProcessData::dispatch($this->data);
    }

    public function failed(\Throwable $exception)
    {
        // \Illuminate\Support\Facades\Log::error('Job failed: ' . $exception->getMessage(), [
        //     'data' => $this->data,
        //     'exception' => $exception,
        // ]);
    }
} 