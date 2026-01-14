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

// Главная страница
Route::get('/', function () {
    return view('welcome');
});

// Аутентификация
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Выход
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// Защищенные маршруты
Route::middleware('auth')->group(function () {
    // Дашборд
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Ресурсные маршруты (CRUD) - В КОНЦЕ!
    // Route::resource('things', ThingController::class); // ЗАКОММЕНТИРУЙ ЭТУ СТРОКУ!
    
    // СНАЧАЛА ВСЕ КАСТОМНЫЕ МАРШРУТЫ things/*
    // ВКЛАДКИ ИЗ ВЫПАДАЮЩЕГО СПИСКА:
    
    // 1. Общий список (уже есть как things.index)
    Route::get('/things', [ThingController::class, 'index'])->name('things.index');
    
    // 2. Мои вещи
    Route::get('/things/my', [ThingController::class, 'my'])->name('things.my');
    
    // 3. Вещи в ремонте/мойке
    Route::get('/things/repair', [ThingController::class, 'repair'])->name('things.repair');
    
    // 4. Вещи в работе
    Route::get('/things/work', [ThingController::class, 'work'])->name('things.work');
    
    // 5. Личные вещи, которые используются другими пользователями
    Route::get('/things/used', [ThingController::class, 'used'])->name('things.used');
    
    // 6. Взятые мной вещи (дополнительно)
    Route::get('/things/borrowed', [ThingController::class, 'borrowed'])->name('things.borrowed');
    
    // 7. Все вещи для администратора
    Route::get('/things/admin/all', [ThingController::class, 'all'])->name('things.admin.all');
    
    // 8. Создание вещи
    Route::get('/things/create', [ThingController::class, 'create'])->name('things.create');
    Route::post('/things', [ThingController::class, 'store'])->name('things.store');
    
    // 9. Показ, редактирование, удаление вещи (с ограничением ID только цифрами)
    Route::get('/things/{thing}', [ThingController::class, 'show'])->name('things.show')->where('thing', '[0-9]+');
    Route::get('/things/{thing}/edit', [ThingController::class, 'edit'])->name('things.edit')->where('thing', '[0-9]+');
    Route::put('/things/{thing}', [ThingController::class, 'update'])->name('things.update')->where('thing', '[0-9]+');
    Route::delete('/things/{thing}', [ThingController::class, 'destroy'])->name('things.destroy')->where('thing', '[0-9]+');
    
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
    // 1. Добавление нового описания
    Route::post('/things/{thing}/add-description', [ThingController::class, 'addDescription'])
        ->name('things.add-description')
        ->where('thing', '[0-9]+');
    
    // 2. Установка текущего описания
    Route::post('/things/{thing}/set-current-description/{description}', 
        [ThingController::class, 'setCurrentDescription'])
        ->name('things.set-current-description')
        ->where('thing', '[0-9]+');
    
    // 3. Редактирование описания
    Route::put('/things/{thing}/update-description/{description}', 
        [ThingController::class, 'updateDescription'])
        ->name('things.update-description')
        ->where('thing', '[0-9]+');
    
    // Места хранения (ресурсный маршрут здесь нормально работает)
    Route::resource('places', PlaceController::class);
    
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