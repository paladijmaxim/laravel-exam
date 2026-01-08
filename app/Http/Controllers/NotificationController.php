<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->with(['thing', 'fromUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('notifications.index', compact('notifications'));
    }

    public function show(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        // НЕ отмечаем как прочитанное здесь
        // Пользователь должен нажать кнопку "Ознакомлен"
        
        return view('notifications.show', compact('notification'));
    }

    public function markAsRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Доступ запрещен'], 403);
            }
            abort(403);
        }

        $notification->markAsRead();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Уведомление отмечено как прочитанное'
            ]);
        }

        return back()->with('success', 'Уведомление отмечено как прочитанное');
    }

    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}