<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        // Real notifications from database if using DatabaseNotification system
        // For now, if unreadNotifications relation is available:
        $dbNotifications = $request->user()->notifications()->latest()->limit(20)->get();
        
        $notifications = $dbNotifications->map(function ($notif) {
            $data = $notif->data;
            return [
                'id' => $notif->id,
                'type' => $data['type'] ?? 'info',
                'title' => $data['title'] ?? 'Notification',
                'message' => $data['message'] ?? '',
                'time' => $notif->created_at->diffForHumans(),
                'read' => $notif->read_at !== null,
            ];
        });

        return Inertia::render('Notifications', [
            'notifications' => $notifications
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return redirect()->back();
    }
}
