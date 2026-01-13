<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class BladeDirectiveServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Директива с параметрами (без закрывающего тега)
        Blade::directive('mything', function ($expression) {
            // Обрабатываем выражение: @mything($thing, 'style')
            $expression = trim($expression, "()");
            
            if (empty($expression)) {
                return '';
            }
            
            // Разделяем на части
            $parts = array_map('trim', explode(',', $expression, 2));
            $thing = $parts[0];
            $type = isset($parts[1]) ? trim($parts[1], " '\"") : 'check';
            
            // Если тип 'check' - это обычное условие
            if ($type === 'check') {
                return "<?php if (Auth::check() && {$thing}->master == Auth::id()): ?>";
            }
            
            // Для других типов возвращаем значение
            return "<?php echo (Auth::check() && {$thing}->master == Auth::id()) ? '" . 
                   $this->getOutput($type) . "' : ''; ?>";
        });
        
        // Закрывающая директива
        Blade::directive('endmything', function () {
            return '<?php endif; ?>';
        });

        Blade::directive('navactive', function ($expression) {
            // Убираем скобки и кавычки
            $expression = trim($expression, "() '\"");
            
            // Если есть запятая - значит есть второй параметр (класс)
            if (str_contains($expression, ',')) {
                [$route, $class] = explode(',', $expression, 2);
                $route = trim($route, " '\"");
                $class = trim($class, " '\"");
            } else {
                $route = $expression;
                $class = 'active';
            }
            
            // Проверяем, является ли это паттерном (содержит *)
            if (str_contains($route, '*')) {
                return "<?php if (request()->routeIs('{$route}')) echo '{$class}'; ?>";
            }
            
            // Для конкретного маршрута
            return "<?php if (request()->routeIs('{$route}')) echo '{$class}'; ?>";
        });

        Blade::directive('specialthing', function ($expression) {
            // @specialthing($thing) - возвращает класс для стиля
            return "<?php 
                \$thing = {$expression};
                if (\$thing->isInUse()) {
                    \$place = \$thing->currentPlace();
                    if (\$place && \$place->repair) {
                        echo 'thing-repair';
                    } elseif (\$place && \$place->work) {
                        echo 'thing-work';
                    }
                }
            ?>";
        });
    }
    
    private function getOutput(string $type): string
    {
        return match($type) {
            'style' => 'style="background-color: #e8f5e9; border-left: 4px solid #28a745;"',
            'class' => 'my-thing-highlight',
            'badge' => '<span class="badge bg-success"><i class="fas fa-user"></i> Моя</span>',
            'icon' => 'text-success',
            'owner' => '<span class="text-success fw-bold"><i class="fas fa-user-check"></i> Вы</span>',
            default => ''
        };
    }
}