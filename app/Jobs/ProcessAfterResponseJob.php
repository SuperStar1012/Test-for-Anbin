<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\RedisUpdated;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;


class ProcessAfterResponseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $fileName;

    public function __construct($filePath, $fileName)
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
    }

    public function handle()
    {
        // Now, handle your file here using $this->filePath and $this->fileName
        $storagePath = "uploads/" . $this->fileName;

        // Store the file in your public disk
        Storage::disk('public')->put($storagePath, file_get_contents($this->filePath));

        // Signal that the process is complete
        Redis::set("status:{$this->fileName}", 'complete');
        event(new RedisUpdated(['status' => 'complete']));
    }
}
