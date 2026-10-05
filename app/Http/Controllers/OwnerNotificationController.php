<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerNotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated owner.
     */
    public function index(Request $request): View
    {
        $owner = $request->user();
        $notifications = $owner->notifications()->paginate(20);
        $unreadCount = $owner->unreadNotifications()->count();

        return view('owner.notifications.index', compact('owner', 'notifications', 'unreadCount'));
    }

    /**
     * Get recent notifications in JSON format (used by top header notification bell).
     */
    public function recent(Request $request): JsonResponse
    {
        $owner = $request->user();
        $unreadCount = $owner->unreadNotifications()->count();
        $recentNotifications = $owner->notifications()
            ->take(8)
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'read' => $notif->read(),
                    'created_at_human' => $notif->created_at->diffForHumans(),
                    'data' => $notif->data,
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $recentNotifications,
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $owner = $request->user();
        $notification = $owner->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            $reservationId = $notification->data['reservation_id'] ?? null;

            if ($request->wantsJson()) {
                return response()->json(['success' => true]);
            }

            if ($reservationId) {
                return redirect()->route('owner.reservations.index', ['highlight' => $reservationId]);
            }
        }

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): RedirectResponse|JsonResponse
    {
        $owner = $request->user();
        $owner->unreadNotifications->markAsRead();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
