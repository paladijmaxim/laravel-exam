<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class BladeDirectiveServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blade::directive('mything', function ($expression) {
            // выражение: @mything($thing, 'style')
            $expression = trim($expression, "()");
            
            if (empty($expression)) {
                return '';
            }
            
            $parts = array_map('trim', explode(',', $expression, 2)); // разделение на части, array_map('trim' убирает проблемы в разных частях
            $thing = $parts[0];
            $type = isset($parts[1]) ? trim($parts[1], " '\"") : 'check';
            
            if ($type === 'check') { // если тип 'check'
                return "<?php if (Auth::check() && {$thing}->master == Auth::id()): ?>";
            }
            
            return "<?php echo (Auth::check() && {$thing}->master == Auth::id()) ? '" .  // для других типов возвращаем значение
                   $this->getOutput($type) . "' : ''; ?>";
        });
        
        Blade::directive('endmything', function () { // закрывающая директива
            return '<?php endif; ?>';
        });

        Blade::directive('navactive', function ($expression) {
            $expression = trim($expression, "() '\"");
            
            // если есть запятая - значит есть второй параметр
            if (str_contains($expression, ',')) {
                [$route, $class] = explode(',', $expression, 2);
                $route = trim($route, " '\"");
                $class = trim($class, " '\"");
            } else {
                $route = $expression;
                $class = 'active';
            }
            
            if (str_contains($route, '*')) { // проверка является ли это паттерном (содержит *)
                return "<?php if (request()->routeIs('{$route}')) echo '{$class}'; ?>";
            }
              
            return "<?php if (request()->routeIs('{$route}')) echo '{$class}'; ?>"; // Для конкретного маршрута
        });

        Blade::directive('specialthing', function ($thing) {
            return "<?php
                \$status = {$thing}->isInSpecialPlace();
                if (\$status === 'repair') echo 'thing-repair';
                elseif (\$status === 'work') echo 'thing-work';
            ?>";
        });
    }
    
    private function getOutput(string $type): string
    {
        return match($type) {
            'style' => 'style="background-color: #e8f5e9; border-left: 4px solid #28a745;"',
            'class' => 'my-thing-highlight',
            'badge' => '<span class="badge bg-success">Моя</span>',
            'icon' => 'text-success',
            'owner' => '<span class="text-success fw-bold"><i class="fas fa-user-check"></i> Вы</span>',
            default => ''
        };
    }
}