<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationStoreRequest;
use App\Http\Requests\NotificationUpdateRequest;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Notification::class, 'notification');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = $user->notifications();

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by read status
        if ($request->has('is_read')) {
            $query->where('is_read', $request->boolean('is_read'));
        }

        // Sort by created_at by default, newest first
        $query->orderBy('created_at', 'desc');

        $notifications = $query->paginate($request->input('per_page', 10));

        return response()->json($notifications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NotificationStoreRequest $request): JsonResponse
    {
        $notification = Notification::create($request->validated());

        return response()->json($notification, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification): JsonResponse
    {
        return response()->json($notification);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NotificationUpdateRequest $request, Notification $notification): JsonResponse
    {
        $notification->update($request->validated());

        return response()->json($notification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification): JsonResponse
    {
        $notification->delete();

        return response()->json(null, 204);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification): JsonResponse
    {
        $this->authorize('update', $notification);

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json($notification);
    }

    /**
     * Mark a notification as unread.
     */
    public function markAsUnread(Notification $notification): JsonResponse
    {
        $this->authorize('update', $notification);

        $notification->update([
            'is_read' => false,
            'read_at' => null,
        ]);

        return response()->json($notification);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = Auth::user();
        $user->notifications()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    /**
     * Get unread notifications count.
     */
    public function unreadCount(): JsonResponse
    {
        $user = Auth::user();
        $count = $user->notifications()->where('is_read', false)->count();

        return response()->json(['count' => $count]);
    }
}
