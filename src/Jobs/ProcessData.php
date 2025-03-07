<?php

namespace Tmt\TmtLat\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;
    private $queue;
    private $tries;
    private $timeout;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->queue = config('tmt-lat.queue', 'tmt');
        $this->tries = config('tmt-lat.queue.tries', 3);
        $this->timeout = config('tmt-lat.queue.timeout', 30);
    }

    public function handle()
    {
        $apiUrl = config('tmt-lat.api.url');
        $apiKey = config('tmt-lat.api.key');

        try {
            Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ])->post($apiUrl, [
                'data' => $this->data
            ]);
        } catch (\Exception $e) {
            // Log::error('Failed to send data to API: ' . $e->getMessage());
            // throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Data processing failed: ' . $exception->getMessage(), [
            'data' => $this->data,
            'exception' => $exception
        ]);
    }
} 