<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index() // список увед
    {
        $notifications = Notification::where('user_id', Auth::id()) // только текущий пользователь 
            ->with(['thing', 'fromUser']) // загружает связанные модели одним запросом
            ->orderBy('created_at', 'desc') // сортировка по убыванию
            ->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function show(Notification $notification) // просмотр уведомения 
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }
        return view('notifications.show', compact('notification'));
    }

    public function markAsRead(Request $request, Notification $notification) // отметка как прочитанное
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

    public function getUnreadCount() // количество непрочитанных
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->count();
        return response()->json(['count' => $count]);
    }
}