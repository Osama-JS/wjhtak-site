<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $userId;
    public string $type;
    public string $title;
    public string $body;
    public array $data;

    /**
     * Max retries before marking as failed.
     */
    public int $tries = 3;

    /**
     * Retry delay in seconds (exponential: 10s, 30s, 60s).
     */
    public function backoff(): array
    {
        return [10, 30, 60];
    }

    public function __construct(int $userId, string $type, string $title, string $body, array $data = [])
    {
        $this->userId = $userId;
        $this->type = $type;
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;

        // Run on dedicated notification queue
        $this->onQueue('notifications');
    }

    public function handle(NotificationService $notificationService): void
    {
        $user = User::find($this->userId);

        if (!$user) {
            Log::warning("SendNotificationJob: User #{$this->userId} not found, skipping.");
            return;
        }

        $notificationService->processNotification(
            $user,
            $this->type,
            $this->title,
            $this->body,
            $this->data
        );
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SendNotificationJob failed for user #{$this->userId}: {$exception->getMessage()}", [
            'type' => $this->type,
            'title' => $this->title,
        ]);
    }
}
