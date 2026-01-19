<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ThingController;
use App\Http\Controllers\Api\PlaceController;
use App\Http\Controllers\Api\ArchivedThingController;
use App\Http\Controllers\Api\NotificationController;

Route::prefix('v1')->group(function () {
    // Аутентификация
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');
    // Публичный доступ к вещам
    Route::get('/things', [ThingController::class, 'index'])->name('api.things.index');
    Route::get('/things/{id}', [ThingController::class, 'show'])->name('api.things.show');
});

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Аутентификация
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/user', [AuthController::class, 'user'])->name('api.user');
    // Вещи
    Route::post('/things', [ThingController::class, 'store'])->name('api.things.store');
    Route::put('/things/{id}', [ThingController::class, 'update'])->name('api.things.update');
    Route::delete('/things/{id}', [ThingController::class, 'destroy'])->name('api.things.destroy');
    // Мои вещи
    Route::get('/my-things', [ThingController::class, 'myThings'])->name('api.things.my');
    // Взятые вещи
    Route::get('/borrowed-things', [ThingController::class, 'borrowedThings'])->name('api.things.borrowed');
    // Передача вещей
    Route::post('/things/{id}/transfer', [ThingController::class, 'transfer'])->name('api.things.transfer');
    Route::post('/things/{id}/return', [ThingController::class, 'returnThing'])->name('api.things.return');
    // Описания вещей
    Route::post('/things/{id}/descriptions', [ThingController::class, 'addDescription'])->name('api.things.add-description');
    // Админские маршруты
    Route::get('/admin/things', [ThingController::class, 'adminAll'])->name('api.things.admin.all');
    // Места 
    Route::apiResource('places', PlaceController::class); // создает только 5 маршрутов т е без create и edit
    // Архив 
    Route::prefix('archived')->group(function () {
        Route::get('/', [ArchivedThingController::class, 'index'])->name('api.archived.index');
        Route::get('/{id}', [ArchivedThingController::class, 'show'])->name('api.archived.show');
        Route::post('/{id}/restore', [ArchivedThingController::class, 'restore'])->name('api.archived.restore');
    });
    // Уведомления 
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('api.notifications.index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('api.notifications.read');
    });
});