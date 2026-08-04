<?php
namespace App\Jobs;

abstract class Job {
    abstract public function handle(): void;

    /**
     * Called if the job fails after all retries
     */
    public function failed(\Throwable $e): void {
        error_log("[Job Failed] " . static::class . ": " . $e->getMessage());
    }

    /**
     * Seconds to wait before retrying
     */
    public function retryDelay(): int {
        return 30;
    }

    /**
     * Maximum number of attempts
     */
    public function maxTries(): int {
        return 3;
    }

    /**
     * Queue name for this job
     */
    public function queue(): string {
        return 'default';
    }
}
