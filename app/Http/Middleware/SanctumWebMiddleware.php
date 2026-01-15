<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanctumWebMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Получаем токен из куки
        $token = $request->cookie('sanctum_token');
        
        if (!$token) {
            // Пробуем получить из сессии
            $token = session('sanctum_token');
        }
        
        if ($token && !$request->bearerToken()) {
            // Устанавливаем заголовок Authorization для Sanctum
            $request->headers->set('Authorization', 'Bearer ' . $token);
        }

        return $next($request);
    }
}