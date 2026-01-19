<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        // получение пользователя из запроса (работает с Sanctum)
        $user = $request->user();
        
        // удаление всех токенов Sanctum
        if ($user && method_exists($user, 'tokens')) {
            try {
                $user->tokens()->delete();
            } catch (\Exception $e) {
            }
        }
        
        // выход из сессионной аутентификации (web guard)
        // аутентифицирован ли пользователь через сессии
        if (Auth::guard('web')->check()) {
            try {
                Auth::guard('web')->logout();
            } catch (\Exception $e) {
                // игнор ошибку если guard не поддерживает logout
            }
        }
        
        // очщение всех данных сессии
        session()->flush(); // Полностью очищаем сессию
        
        // создание новой сессии
        $request->session()->regenerate();
        
        // ответ с удаленными куками
        return redirect('/')
            ->with('success', 'Вы успешно вышли из системы')
            ->withCookie(\Cookie::forget('sanctum_token'))
            ->withCookie(\Cookie::forget('laravel_session'))
            ->withCookie(\Cookie::forget(session()->getName()));
    }
}