<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        // 1. Получаем пользователя из запроса (работает с Sanctum)
        $user = $request->user();
        
        // 2. Удаляем все токены Sanctum
        if ($user && method_exists($user, 'tokens')) {
            try {
                $user->tokens()->delete();
            } catch (\Exception $e) {
                // Игнорируем ошибки при удалении токенов
            }
        }
        
        // 3. Выход из сессионной аутентификации (web guard)
        // Проверяем, аутентифицирован ли пользователь через сессии
        if (Auth::guard('web')->check()) {
            try {
                Auth::guard('web')->logout();
            } catch (\Exception $e) {
                // Игнорируем ошибку если guard не поддерживает logout
            }
        }
        
        // 4. Очищаем все данные сессии
        session()->flush(); // Полностью очищаем сессию
        
        // 5. Создаем новую сессию
        $request->session()->regenerate();
        
        // 6. Возвращаем ответ с удаленными куками
        return redirect('/')
            ->with('success', 'Вы успешно вышли из системы')
            ->withCookie(\Cookie::forget('sanctum_token'))
            ->withCookie(\Cookie::forget('laravel_session'))
            ->withCookie(\Cookie::forget(session()->getName()));
    }
}