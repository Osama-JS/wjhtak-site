<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Show the notifications management page.
     */
    public function index()
    {
        $stats = [
            'total' => Notification::count(),
            'unread' => Notification::where('is_read', false)->count(),
            'today' => Notification::whereDate('created_at', today())->count(),
            'users_with_fcm' => User::whereNotNull('fcm_token')->where('fcm_token', '!=', '')->count(),
            'total_users' => User::count(),
        ];

        return view('admin.notifications.index', compact('stats'));
    }

    /**
     * Get notifications history for DataTable.
     */
    public function getData(Request $request)
    {
        $query = Notification::with('user')->latest();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $notifications = $query->paginate(25);

        return response()->json([
            'data' => $notifications->map(function ($n) {
                return [
                    'id' => $n->id,
                    'type' => $n->type,
                    'type_label' => $this->getTypeLabel($n->type),
                    'title' => $n->title,
                    'content' => \Illuminate\Support\Str::limit($n->content, 60),
                    'user_name' => $n->user ? $n->user->name : __('All Users'),
                    'user_email' => $n->user ? $n->user->email : '-',
                    'is_read' => $n->is_read,
                    'created_at' => $n->created_at->format('Y-m-d H:i'),
                    'time_ago' => $n->created_at->diffForHumans(),
                ];
            }),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    /**
     * Search users for autocomplete.
     */
    public function searchUsers(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%")
              ->orWhere('phone', 'like', "%{$query}%");
        })
        ->select('id', 'name', 'email', 'phone', 'fcm_token')
        ->limit(20)
        ->get()
        ->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'has_fcm' => !empty($user->fcm_token),
            ];
        });

        return response()->json($users);
    }

    /**
     * Send notification (broadcast or targeted).
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'body_ar' => 'required|string|max:1000',
            'body_en' => 'required|string|max:1000',
            'type' => 'required|string|in:general,promotion,new_trip,booking_reminder,favorite_trip_update',
            'target' => 'required|in:all,selected',
            'user_ids' => 'required_if:target,selected|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $type = $request->type;
        $titleAr = $request->title_ar;
        $titleEn = $request->title_en;
        $bodyAr = $request->body_ar;
        $bodyEn = $request->body_en;

        // Use Arabic as default (or based on app locale)
        $title = $titleAr;
        $body = $bodyAr;

        $data = [
            'title_ar' => $titleAr,
            'title_en' => $titleEn,
            'body_ar' => $bodyAr,
            'body_en' => $bodyEn,
            'sent_by' => auth()->user()->name ?? 'Admin',
        ];

        $sentCount = 0;

        try {
            if ($request->target === 'all') {
                // Broadcast to all users with FCM tokens
                $this->notificationService->sendToAll($type, $title, $body, $data);
                $sentCount = User::whereNotNull('fcm_token')->where('fcm_token', '!=', '')->count();

                Log::info("Admin broadcast notification sent to {$sentCount} users", [
                    'type' => $type,
                    'title' => $title,
                    'admin' => auth()->user()->name ?? 'Admin',
                ]);
            } else {
                // Targeted notification to selected users
                $userIds = $request->user_ids;
                $this->notificationService->sendToUsers($userIds, $type, $title, $body, $data);
                $sentCount = count($userIds);

                Log::info("Admin targeted notification sent to {$sentCount} users", [
                    'type' => $type,
                    'title' => $title,
                    'user_ids' => $userIds,
                    'admin' => auth()->user()->name ?? 'Admin',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => __('Notification sent successfully to :count users', ['count' => $sentCount]),
                'sent_count' => $sentCount,
            ]);
        } catch (\Exception $e) {
            Log::error("Admin notification send error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Failed to send notification: ') . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a notification from history.
     */
    public function destroy($id)
    {
        $notification = Notification::find($id);
        if (!$notification) {
            return response()->json(['success' => false, 'message' => __('Notification not found')], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => __('Notification deleted successfully'),
        ]);
    }

    /**
     * Get human-readable type label.
     */
    private function getTypeLabel(string $type): string
    {
        return match ($type) {
            'booking_confirmed' => __('Booking Confirmed'),
            'booking_cancelled' => __('Booking Cancelled'),
            'payment_success' => __('Payment Success'),
            'payment_failed' => __('Payment Failed'),
            'booking_reminder' => __('Booking Reminder'),
            'favorite_trip_update' => __('Favorite Update'),
            'new_trip' => __('New Trip'),
            'promotion' => __('Promotion'),
            'general' => __('General'),
            default => $type,
        };
    }
}
