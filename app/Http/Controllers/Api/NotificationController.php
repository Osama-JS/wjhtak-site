<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class NotificationController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get user notifications (paginated).
     */
    #[OA\Get(
        path: "/api/v1/notifications",
        summary: "Get my notifications",
        operationId: "getMyNotifications",
        description: "Retrieve a paginated list of stored notifications for the authenticated user.\n\nOnly **storable** notification types are returned (e.g., booking, payment, favorites). Broadcast-only types (new_trip, promotion, general) are not stored.\n\nResults are ordered by most recent first.",
        tags: ["Notifications"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "Response language: ar or en",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            ),
            new OA\Parameter(
                name: "page",
                in: "query",
                description: "Page number",
                required: false,
                schema: new OA\Schema(type: "integer", default: 1)
            ),
            new OA\Parameter(
                name: "type",
                in: "query",
                description: "Filter by notification type",
                required: false,
                schema: new OA\Schema(type: "string", enum: [
                    "booking_confirmed", "booking_cancelled",
                    "payment_success", "payment_failed",
                    "booking_reminder", "favorite_trip_update"
                ])
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Notifications retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Notifications retrieved successfully"),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "notifications", type: "array", items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "type", type: "string", example: "payment_success"),
                                    new OA\Property(property: "title", type: "string", example: "Payment Successful"),
                                    new OA\Property(property: "content", type: "string", example: "Your payment of 500 SAR was successful."),
                                    new OA\Property(property: "icon", type: "string", example: "payment_success"),
                                    new OA\Property(property: "is_read", type: "boolean", example: false),
                                    new OA\Property(property: "data", type: "object", nullable: true),
                                    new OA\Property(property: "created_at", type: "string", example: "2024-05-20T10:00:00Z"),
                                ]
                            )),
                            new OA\Property(property: "unread_count", type: "integer", example: 3),
                            new OA\Property(property: "pagination", type: "object", properties: [
                                new OA\Property(property: "current_page", type: "integer", example: 1),
                                new OA\Property(property: "last_page", type: "integer", example: 5),
                                new OA\Property(property: "per_page", type: "integer", example: 20),
                                new OA\Property(property: "total", type: "integer", example: 95),
                            ])
                        ])
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return $this->apiResponse(true, 'Unauthenticated', null, null, 401);
        }

        $query = Notification::forUser($user->id)->latest();

        // Filter by type if provided
        if ($request->has('type')) {
            $query->ofType($request->type);
        }

        $notifications = $query->paginate(20);

        $unreadCount = Notification::forUser($user->id)->unread()->count();

        return $this->apiResponse(false, __('Notifications retrieved successfully'), [
            'notifications' => $notifications->map(function ($n) {
                return [
                    'id' => $n->id,
                    'type' => $n->type,
                    'title' => $n->title,
                    'content' => $n->content,
                    'icon' => $n->icon,
                    'is_read' => $n->is_read,
                    'data' => $n->data,
                    'created_at' => $n->created_at->toIso8601String(),
                ];
            }),
            'unread_count' => $unreadCount,
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    /**
     * Get unread notifications count.
     */
    #[OA\Get(
        path: "/api/v1/notifications/unread-count",
        summary: "Get unread count",
        operationId: "getUnreadNotificationsCount",
        description: "Returns the number of unread notifications for the authenticated user.",
        tags: ["Notifications"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Unread count retrieved",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Unread count retrieved"),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "unread_count", type: "integer", example: 5),
                        ])
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function unreadCount(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return $this->apiResponse(true, 'Unauthenticated', null, null, 401);
        }

        $count = Notification::forUser($user->id)->unread()->count();

        return $this->apiResponse(false, __('Unread count retrieved'), [
            'unread_count' => $count,
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    #[OA\Post(
        path: "/api/v1/notifications/{id}/read",
        summary: "Mark notification as read",
        operationId: "markNotificationAsRead",
        description: "Mark a specific notification as read for the authenticated user.",
        tags: ["Notifications"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "Notification ID",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Notification marked as read",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Notification marked as read"),
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Notification not found"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function markAsRead($id): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return $this->apiResponse(true, 'Unauthenticated', null, null, 401);
        }

        $updated = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->update(['is_read' => true]);

        if (!$updated) {
            return $this->apiResponse(true, __('Notification not found'), null, null, 404);
        }

        return $this->apiResponse(false, __('Notification marked as read'));
    }

    /**
     * Mark all notifications as read.
     */
    #[OA\Post(
        path: "/api/v1/notifications/read-all",
        summary: "Mark all as read",
        operationId: "markAllNotificationsAsRead",
        description: "Mark all unread notifications as read for the authenticated user.",
        tags: ["Notifications"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "All notifications marked as read",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "All notifications marked as read"),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "updated_count", type: "integer", example: 5),
                        ])
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return $this->apiResponse(true, 'Unauthenticated', null, null, 401);
        }

        $count = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return $this->apiResponse(false, __('All notifications marked as read'), [
            'updated_count' => $count,
        ]);
    }

    /**
     * Delete a notification.
     */
    #[OA\Delete(
        path: "/api/v1/notifications/{id}",
        summary: "Delete a notification",
        operationId: "deleteNotification",
        description: "Delete a specific notification for the authenticated user.",
        tags: ["Notifications"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "Notification ID",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Notification deleted",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Notification deleted"),
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Notification not found"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function destroy($id): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return $this->apiResponse(true, 'Unauthenticated', null, null, 401);
        }

        $deleted = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->delete();

        if (!$deleted) {
            return $this->apiResponse(true, __('Notification not found'), null, null, 404);
        }

        return $this->apiResponse(false, __('Notification deleted'));
    }
}
