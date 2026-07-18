<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return view(
            'notifications.index',
            compact('notifications')
        );
    }

    public function markAsRead(
        Request $request,
        DatabaseNotification $notification
    ) {
        abort_unless(
            $notification->notifiable_id === $request->user()->id
            && $notification->notifiable_type === $request->user()::class,
            403
        );

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $url = $notification->data['url'] ?? null;

        if ($url) {
            return redirect($url);
        }

        return redirect()
            ->route('notifications.index')
            ->with('success', 'تم تحديد الإشعار كمقروء.');
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()
            ->unreadNotifications
            ->markAsRead();

        return redirect()
            ->route('notifications.index')
            ->with('success', 'تم تحديد جميع الإشعارات كمقروءة.');
    }

    public function destroy(
        Request $request,
        DatabaseNotification $notification
    ) {
        abort_unless(
            $notification->notifiable_id === $request->user()->id
            && $notification->notifiable_type === $request->user()::class,
            403
        );

        $notification->delete();

        return back()->with(
            'success',
            'تم حذف الإشعار.'
        );
    }
}
