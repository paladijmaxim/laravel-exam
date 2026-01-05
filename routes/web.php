<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ThingController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\ArchivedThingController;

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
    
    // Ресурсные маршруты (CRUD)
    Route::resource('things', ThingController::class);
    Route::resource('places', PlaceController::class);
    
    // ВКЛАДКИ ИЗ ВЫПАДАЮЩЕГО СПИСКА:
    
    // 1. Общий список (уже есть как things.index)
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
    
    // Передача вещи
    Route::get('/things/{thing}/transfer-form', [ThingController::class, 'transferForm'])->name('things.transfer.form');
    Route::post('/things/{thing}/transfer', [ThingController::class, 'transfer'])->name('things.transfer');
    
    // Возврат вещи
    Route::post('/things/{thing}/return', [ThingController::class, 'return'])->name('things.return');
    Route::post('/things/{thing}/add-description', [ThingController::class, 'addDescription'])->name('things.add-description');
Route::post('/things/{thing}/set-current-description/{description}', [ThingController::class, 'setCurrentDescription'])->name('things.set-current-description');
    // Архив удаленных вещей
    Route::prefix('archived')->name('archived.')->middleware('auth')->group(function () {
        Route::get('/', [ArchivedThingController::class, 'index'])->name('index');
        Route::get('/{id}', [ArchivedThingController::class, 'show'])->name('show');
        Route::post('/{id}/restore', [ArchivedThingController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [ArchivedThingController::class, 'forceDelete'])->name('force-delete');
    });
});