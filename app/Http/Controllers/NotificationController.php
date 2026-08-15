<?php
// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use App\Models\AppointmentNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /** Mark one notification as read, redirect to guidance page. */
    public function markRead(AppointmentNotification $notification)
    {
        abort_if($notification->user_id !== Auth::id(), 403);
        $notification->markAsRead();
        return redirect()->route('guidance');
    }

    /** Mark ALL unread notifications as read. */
    public function markAllRead(Request $request)
    {
        AppointmentNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $request->expectsJson()
            ? response()->json(['success' => true])
            : back();
    }

    // ── ADD THIS ─────────────────────────────────────────────────────────────
    /** Fetch latest notifications as JSON (used by the polling bell). */
    public function fetch(): \Illuminate\Http\JsonResponse
    {
        $notifications = AppointmentNotification::where('user_id', Auth::id())
            ->with(['appointment', 'referralInvitation.referral'])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($n) => [
                'id'        => $n->id,
                'type'      => $n->type,
                'message'   => $n->message,
                'is_unread' => $n->isUnread(),
                'time_ago'  => $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'unread_count'  => AppointmentNotification::where('user_id', Auth::id())
                                ->whereNull('read_at')->count(),
            'notifications' => $notifications,
        ]);
    }
    // ─────────────────────────────────────────────────────────────────────────
}