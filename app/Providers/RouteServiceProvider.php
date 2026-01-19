<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard'; // константа куда редиректить после входа
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api') // middleware группу 'api'
                ->prefix('api') // все юрл начинаются с /api
                ->group(base_path('routes/api.php')); // подкл файл маршрутов

            Route::middleware('web')
                ->group(base_path('routes/web.php')); // без префикса
        });
    }

    protected function configureRateLimiting(): void // огр запросов
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());  // 60 запросов в минуту ппо user_id или IP
        });
        
        RateLimiter::for('web-sanctum', function (Request $request) {
            return Limit::perMinute(100)->by($request->user()?->id ?: $request->ip());
        });
    }
}