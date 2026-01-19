<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string // определние того, куда перенаправить пользователя
    {
        if ($request->is('api/*') || $request->expectsJson()) { // если это апи запрос или ожидается от нас json
            abort(401, 'Unauthenticated.');
        }
        
        return route('login'); // для веб запросов просто на страницу авторизации 
    }
}