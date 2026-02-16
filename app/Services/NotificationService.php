<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    // ─── Main Public API ───────────────────────────────────

    /**
     * Send a notification to a single user.
     *
     * @param User   $user    Target user
     * @param string $type    Notification type constant (e.g., Notification::TYPE_PAYMENT_SUCCESS)
     * @param string $title   Display title
     * @param string $body    Display body text
     * @param array  $data    Additional payload data (optional)
     * @param bool   $queue   Whether to dispatch via queue (default: true)
     */
    public function sendToUser(User $user, string $type, string $title, string $body, array $data = [], bool $queue = true): void
    {
        if ($queue) {
            SendNotificationJob::dispatch($user->id, $type, $title, $body, $data);
            return;
        }

        $this->processNotification($user, $type, $title, $body, $data);
    }

    /**
     * Send a notification to multiple users.
     */
    public function sendToUsers(array $userIds, string $type, string $title, string $body, array $data = []): void
    {
        foreach ($userIds as $userId) {
            SendNotificationJob::dispatch($userId, $type, $title, $body, $data);
        }
    }

    /**
     * Send a notification to ALL users (broadcast).
     * Useful for new_trip, promotion, general announcements.
     */
    public function sendToAll(string $type, string $title, string $body, array $data = []): void
    {
        User::whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '')
            ->select('id')
            ->chunk(100, function ($users) use ($type, $title, $body, $data) {
                foreach ($users as $user) {
                    SendNotificationJob::dispatch($user->id, $type, $title, $body, $data);
                }
            });
    }

    /**
     * Process a notification: store (if storable type) + send push.
     * Called by the Job or directly when queue=false.
     */
    public function processNotification(User $user, string $type, string $title, string $body, array $data = []): void
    {
        // 1) Store in database if this type should be persisted
        if (Notification::shouldStore($type)) {
            Notification::create([
                'type' => $type,
                'title' => $title,
                'content' => $body,
                'icon' => Notification::iconForType($type),
                'user_id' => $user->id,
                'data' => $data ?: null,
                'is_read' => false,
            ]);

            Log::info("Notification stored: [{$type}] for user #{$user->id}");
        }

        // 2) Send push notification via Firebase
        $pushData = array_merge($data, ['type' => $type]);
        $sent = $this->firebaseService->sendToUser($user, $title, $body, $pushData);

        if ($sent) {
            Log::info("Push notification sent: [{$type}] to user #{$user->id}");
        }
    }

    // ─── Read / Manage ─────────────────────────────────────

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        return Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->update(['is_read' => true]) > 0;
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Get unread count for a user.
     */
    public function getUnreadCount(int $userId): int
    {
        return Notification::forUser($userId)->unread()->count();
    }

    /**
     * Delete a notification.
     */
    public function deleteNotification(int $notificationId, int $userId): bool
    {
        return Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->delete() > 0;
    }
}
