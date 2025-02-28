<?php

namespace App\Jobs;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Random\RandomException;

class LogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $message;

    private array $context;

    /**
     * Create a new job instance.
     */
    public function __construct(string $message, array $context = [])
    {
        $this->message = $message;
        $this->context = $context;
    }

    /**
     * Execute the job.
     *
     * @throws RandomException
     * @throws Exception
     */
    public function handle(): void
    {
        $sleepTime = random_int(5, 20);

        if ($sleepTime > 15) {
            throw new Exception("Job failed: sleep time {$sleepTime} is greater than 12");
        }

        sleep($sleepTime);
        Log::info('test'.$this->message, $this->context);
    }
}
