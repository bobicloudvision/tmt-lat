<?php

namespace Tmt\TmtLat\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class FileScanner implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $ignoredPaths;
    private $queue;
    private $tries;
    private $timeout;

    public function __construct()
    {
        $this->ignoredPaths = config('tmt-lat.ignored_paths');
        $this->queue = config('tmt-lat.queue.files', 'file-sync');
        $this->tries = config('tmt-lat.queue.tries', 3);
        $this->timeout = config('tmt-lat.queue.timeout', 30);
    }

    public function handle()
    {
        $basePath = base_path();
        $files = $this->getFiles($basePath);

        foreach ($files as $file) {
            $relativePath = str_replace($basePath, '', $file);
            if ($this->shouldProcessFile($relativePath)) {
                FileJob::dispatch([
                    'type' => 'file',
                    'path' => $relativePath,
                    'timestamp' => now(),
                ]);
            }
        }
    }

    private function getFiles($path)
    {
        $files = [];
        $items = File::files($path);

        foreach ($items as $item) {
            if ($item->isFile()) {
                $files[] = $item->getPathname();
            } elseif ($item->isDir()) {
                $files = array_merge($files, $this->getFiles($item->getPathname()));
            }
        }

        return $files;
    }

    private function shouldProcessFile($path)
    {
        foreach ($this->ignoredPaths as $ignoredPath) {
            if (strpos($path, $ignoredPath) === 0) {
                return false;
            }
        }

        return true;
    }

    public function failed(\Throwable $exception)
    {
        // Log::error('File scanning failed: ' . $exception->getMessage(), [
        //     'exception' => $exception
        // ]);
    }
} 