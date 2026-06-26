<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function markAsRead(Request $request, string $notificationId): JsonResponse
    {
        $notification = $request->user()
            ->unreadNotifications()
            ->findOrFail($notificationId);

        $notification->markAsRead();

        return response()->json(['status' => 'ok']);
    }
}
