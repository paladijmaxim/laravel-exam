<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ThingController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\ArchivedThingController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes с Sanctum аутентификацией
|--------------------------------------------------------------------------
|
| Все защищенные маршруты используют префикс 'app' и middleware 'auth:sanctum'
| Все маршруты именованы
|
*/

// Главная страница - ДОСТУПНА ВСЕМ
Route::get('/', function () {
    // Получаем общее количество вещей
    $totalThings = \App\Models\Thing::count();
    
    // Получаем последние 5 вещей для показа на welcome странице
    $recentThings = \App\Models\Thing::with(['owner', 'usages' => function($q) {
        $q->latest()->take(1)->with(['user', 'place']);
    }])
    ->whereDoesntHave('usages.place', function($q) {
        $q->where('repair', true)->orWhere('work', true);
    })
    ->latest()
    ->take(5)
    ->get();
    
    return view('welcome', compact('totalThings', 'recentThings'));
})->name('welcome');

// ПУБЛИЧНЫЕ маршруты (доступны всем)

// 1. Просмотр списка вещей
Route::get('/things', [ThingController::class, 'index'])->name('things.index');

// 2. Просмотр отдельной вещи
Route::get('/things/{thing}', [ThingController::class, 'show'])->name('things.show')->where('thing', '[0-9]+');

// Аутентификация (доступна без токена)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// ЗАЩИЩЕННЫЕ маршруты с использованием Sanctum
// Используем PREFIX 'app' и middleware 'auth:sanctum' как требует задание
Route::middleware('auth:sanctum')->group(function () {
    
    // Выход (требует аутентификации через Sanctum)
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    
    // Маршруты с префиксом /app для web-интерфейса
    Route::prefix('app')->name('app.')->group(function () {
        
        // Дашборд
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // ОПЕРАЦИИ С ВЕЩАМИ (создание, редактирование, удаление)
        Route::get('/things/create', [ThingController::class, 'create'])->name('things.create');
        Route::post('/things', [ThingController::class, 'store'])->name('things.store');
        Route::get('/things/{thing}/edit', [ThingController::class, 'edit'])->name('things.edit')->where('thing', '[0-9]+');
        Route::put('/things/{thing}', [ThingController::class, 'update'])->name('things.update')->where('thing', '[0-9]+');
        Route::delete('/things/{thing}', [ThingController::class, 'destroy'])->name('things.destroy')->where('thing', '[0-9]+');
        
        // ВКЛАДКИ ИЗ ВЫПАДАЮЩЕГО СПИСКА
        Route::prefix('things')->name('things.')->group(function () {
            // 1. Мои вещи
            Route::get('/my', [ThingController::class, 'my'])->name('my');
            
            // 2. Вещи в ремонте/мойке
            Route::get('/repair', [ThingController::class, 'repair'])->name('repair');
            
            // 3. Вещи в работе
            Route::get('/work', [ThingController::class, 'work'])->name('work');
            
            // 4. Личные вещи, которые используются другими пользователями
            Route::get('/used', [ThingController::class, 'used'])->name('used');
            
            // 5. Взятые мной вещи
            Route::get('/borrowed', [ThingController::class, 'borrowed'])->name('borrowed');
            
            // 6. Все вещи для администратора
            Route::get('/admin/all', [ThingController::class, 'all'])->name('admin.all');
        });
        
        // Передача вещи
        Route::get('/things/{thing}/transfer-form', [ThingController::class, 'transferForm'])
            ->name('things.transfer.form')
            ->where('thing', '[0-9]+');
        Route::post('/things/{thing}/transfer', [ThingController::class, 'transfer'])
            ->name('things.transfer')
            ->where('thing', '[0-9]+');
        
        // Возврат вещи
        Route::post('/things/{thing}/return', [ThingController::class, 'return'])
            ->name('things.return')
            ->where('thing', '[0-9]+');
        
        // ОПИСАНИЯ ВЕЩЕЙ
        Route::post('/things/{thing}/add-description', [ThingController::class, 'addDescription'])
            ->name('things.add-description')
            ->where('thing', '[0-9]+');
        
        Route::post('/things/{thing}/set-current-description/{description}', 
            [ThingController::class, 'setCurrentDescription'])
            ->name('things.set-current-description')
            ->where('thing', '[0-9]+');
        
        Route::put('/things/{thing}/update-description/{description}', 
            [ThingController::class, 'updateDescription'])
            ->name('things.update-description')
            ->where('thing', '[0-9]+');
        
        // Места хранения (ресурсный маршрут)
        Route::resource('places', PlaceController::class)->except(['show']);
        Route::get('/places/{place}', [PlaceController::class, 'show'])->name('places.show');
        
        // Архив удаленных вещей
        Route::prefix('archived')->name('archived.')->group(function () {
            Route::get('/', [ArchivedThingController::class, 'index'])->name('index');
            Route::get('/{id}', [ArchivedThingController::class, 'show'])->name('show')->where('id', '[0-9]+');
            Route::post('/{id}/restore', [ArchivedThingController::class, 'restore'])->name('restore')->where('id', '[0-9]+');
            Route::delete('/{id}/force-delete', [ArchivedThingController::class, 'forceDelete'])->name('force-delete')->where('id', '[0-9]+');
        });

        // Уведомления
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/{notification}', [NotificationController::class, 'show'])->name('show')->where('notification', '[0-9]+');
            Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read')->where('notification', '[0-9]+');
            Route::get('/unread/count', [NotificationController::class, 'getUnreadCount'])->name('unread.count');
        });
    });
    
    // ДОПОЛНИТЕЛЬНО: оставляем некоторые старые маршруты без префикса для совместимости
    // но они все равно требуют аутентификации через Sanctum
    
    // Дашборд без префикса (для совместимости)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Создание вещи без префикса
    Route::get('/things/create', [ThingController::class, 'create'])->name('things.create');
    Route::post('/things', [ThingController::class, 'store'])->name('things.store');
    
    // Редактирование вещи
    Route::get('/things/{thing}/edit', [ThingController::class, 'edit'])->name('things.edit')->where('thing', '[0-9]+');
    Route::put('/things/{thing}', [ThingController::class, 'update'])->name('things.update')->where('thing', '[0-9]+');
    
    // Удаление вещи
    Route::delete('/things/{thing}', [ThingController::class, 'destroy'])->name('things.destroy')->where('thing', '[0-9]+');
    
    // Мои вещи и другие страницы
    Route::get('/things/my', [ThingController::class, 'my'])->name('things.my');
    Route::get('/things/repair', [ThingController::class, 'repair'])->name('things.repair');
    Route::get('/things/work', [ThingController::class, 'work'])->name('things.work');
    Route::get('/things/used', [ThingController::class, 'used'])->name('things.used');
    Route::get('/things/borrowed', [ThingController::class, 'borrowed'])->name('things.borrowed');
    Route::get('/things/admin/all', [ThingController::class, 'all'])->name('things.admin.all');
    
    // Передача и возврат вещей
    Route::get('/things/{thing}/transfer-form', [ThingController::class, 'transferForm'])
        ->name('things.transfer.form')
        ->where('thing', '[0-9]+');
    Route::post('/things/{thing}/transfer', [ThingController::class, 'transfer'])
        ->name('things.transfer')
        ->where('thing', '[0-9]+');
    Route::post('/things/{thing}/return', [ThingController::class, 'return'])
        ->name('things.return')
        ->where('thing', '[0-9]+');
    
    // Описания вещей
    Route::post('/things/{thing}/add-description', [ThingController::class, 'addDescription'])
        ->name('things.add-description')
        ->where('thing', '[0-9]+');
    
    Route::post('/things/{thing}/set-current-description/{description}', 
        [ThingController::class, 'setCurrentDescription'])
        ->name('things.set-current-description')
        ->where('thing', '[0-9]+');
    
    Route::put('/things/{thing}/update-description/{description}', 
        [ThingController::class, 'updateDescription'])
        ->name('things.update-description')
        ->where('thing', '[0-9]+');
    
    // Места хранения
    Route::resource('places', PlaceController::class)->except(['show']);
    Route::get('/places/{place}', [PlaceController::class, 'show'])->name('places.show');
    
    // Архив
    Route::prefix('archived')->name('archived.')->group(function () {
        Route::get('/', [ArchivedThingController::class, 'index'])->name('index');
        Route::get('/{id}', [ArchivedThingController::class, 'show'])->name('show')->where('id', '[0-9]+');
        Route::post('/{id}/restore', [ArchivedThingController::class, 'restore'])->name('restore')->where('id', '[0-9]+');
        Route::delete('/{id}/force-delete', [ArchivedThingController::class, 'forceDelete'])->name('force-delete')->where('id', '[0-9]+');
    });

    // Уведомления
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/{notification}', [NotificationController::class, 'show'])->name('show')->where('notification', '[0-9]+');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read')->where('notification', '[0-9]+');
        Route::get('/unread/count', [NotificationController::class, 'getUnreadCount'])->name('unread.count');
    });
});