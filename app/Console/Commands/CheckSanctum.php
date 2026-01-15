<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class CheckSanctum extends Command
{
    protected $signature = 'check:sanctum';
    protected $description = 'Проверка выполнения задания по Sanctum';

    public function handle()
    {
        $this->info('=== ПРОВЕРКА ВЫПОЛНЕНИЯ ЗАДАНИЯ ===');
        
        // Получаем все маршруты web.php
        $webRoutes = collect(Route::getRoutes()->getRoutes())
            ->filter(fn($route) => in_array('web', $route->gatherMiddleware()));
        
        $this->checkRouteRequirements($webRoutes);
        
        return 0;
    }
    
    private function checkRouteRequirements($routes)
    {
        $requirements = [
            'sanctum_used' => false,
            'prefix_used' => false,
            'all_named' => true,
            'middleware_groups' => false,
        ];
        
        $protectedRoutes = [];
        $publicRoutes = [];
        
        foreach ($routes as $route) {
            $middleware = $route->gatherMiddleware();
            $uri = $route->uri();
            $name = $route->getName();
            $prefix = $route->getPrefix();
            
            // 1. Проверяем использование auth:sanctum
            if (in_array('auth:sanctum', $middleware)) {
                $requirements['sanctum_used'] = true;
                $protectedRoutes[] = $uri;
            } else {
                $publicRoutes[] = $uri;
            }
            
            // 2. Проверяем использование префикса
            if ($prefix && strpos($prefix, 'app') !== false) {
                $requirements['prefix_used'] = true;
            }
            
            // 3. Проверяем именование маршрутов
            if (empty($name) && !str_starts_with($uri, '_')) {
                $requirements['all_named'] = false;
                $this->warn("Маршрут {$uri} не имеет имени!");
            }
            
            // 4. Проверяем использование middleware групп
            if (in_array('auth:sanctum', $middleware) && 
                !empty($route->getAction('middleware'))) {
                $requirements['middleware_groups'] = true;
            }
        }
        
        // Вывод результатов
        $this->line("\n📊 РЕЗУЛЬТАТЫ ПРОВЕРКИ:");
        $this->line(str_repeat('-', 50));
        
        foreach ($requirements as $key => $value) {
            $status = $value ? '✅' : '❌';
            $description = $this->getRequirementDescription($key);
            $this->line("{$status} {$description}");
        }
        
        $this->line("\n🔒 ЗАЩИЩЕННЫЕ маршруты (" . count($protectedRoutes) . "):");
        foreach (array_slice($protectedRoutes, 0, 10) as $route) {
            $this->line("  • {$route}");
        }
        if (count($protectedRoutes) > 10) {
            $this->line("  ... и еще " . (count($protectedRoutes) - 10) . " маршрутов");
        }
        
        $this->line("\n🌐 ПУБЛИЧНЫЕ маршруты (" . count($publicRoutes) . "):");
        foreach (array_slice($publicRoutes, 0, 10) as $route) {
            $this->line("  • {$route}");
        }
        
        $this->line("\n" . str_repeat('=', 50));
        
        // Итоговая оценка
        $passed = count(array_filter($requirements));
        $total = count($requirements);
        
        if ($passed === $total) {
            $this->info("✅ ВСЕ УСЛОВИЯ ВЫПОЛНЕНЫ! ({$passed}/{$total})");
        } else {
            $this->error("❌ ВЫПОЛНЕНО {$passed} из {$total} условий");
        }
    }
    
    private function getRequirementDescription($key)
    {
        $descriptions = [
            'sanctum_used' => 'Sanctum используется для проверки запросов',
            'prefix_used' => 'Используется префикс для маршрутов',
            'all_named' => 'Все маршруты именованы',
            'middleware_groups' => 'Используются middleware группы',
        ];
        
        return $descriptions[$key] ?? $key;
    }
}