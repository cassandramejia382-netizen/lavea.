<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    public function markAsRead(Request $request, string $notificationId): RedirectResponse|Response
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $notificationId)
            ->firstOrFail();

        $notification->markAsRead();

        if ($request->expectsJson()) {
            return response()->noContent();
        }

        return back();
    }
}
