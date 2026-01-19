<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ThingController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\ArchivedThingController;
use App\Http\Controllers\NotificationController;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/things', [ThingController::class, 'index'])->name('things.index');
Route::get('/things/{thing}', [ThingController::class, 'show'])->name('things.show')->where('thing', '[0-9]+');
// аутентификация 
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});
// закрытые маршруты с использованием Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout'); // Выход 
    // Маршруты с префиксом /app для web-интерфейса
    Route::prefix('app')->name('app.')->group(function () {
        // Дашборд
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        // crud things
        Route::get('/things/create', [ThingController::class, 'create'])->name('things.create');
        Route::post('/things', [ThingController::class, 'store'])->name('things.store');
        Route::get('/things/{thing}/edit', [ThingController::class, 'edit'])->name('things.edit')->where('thing', '[0-9]+');
        Route::put('/things/{thing}', [ThingController::class, 'update'])->name('things.update')->where('thing', '[0-9]+');
        Route::delete('/things/{thing}', [ThingController::class, 'destroy'])->name('things.destroy')->where('thing', '[0-9]+');
        // вып список
        Route::prefix('things')->name('things.')->group(function () {
            Route::get('/my', [ThingController::class, 'my'])->name('my');
            Route::get('/repair', [ThingController::class, 'repair'])->name('repair');
            Route::get('/work', [ThingController::class, 'work'])->name('work');
            Route::get('/used', [ThingController::class, 'used'])->name('used');
            Route::get('/borrowed', [ThingController::class, 'borrowed'])->name('borrowed');
            Route::get('/admin/all', [ThingController::class, 'all'])->name('admin.all');
        });
        // Передача вещи
        Route::get('/things/{thing}/transfer-form', [ThingController::class, 'transferForm'])->name('things.transfer.form')->where('thing', '[0-9]+');
        Route::post('/things/{thing}/transfer', [ThingController::class, 'transfer'])->name('things.transfer')->where('thing', '[0-9]+');
        // Возврат вещи
        Route::post('/things/{thing}/return', [ThingController::class, 'return'])->name('things.return')->where('thing', '[0-9]+');
        // описания вещей
        Route::post('/things/{thing}/add-description', [ThingController::class, 'addDescription'])->name('things.add-description')->where('thing', '[0-9]+');
        Route::post('/things/{thing}/set-current-description/{description}', [ThingController::class, 'setCurrentDescription'])->name('things.set-current-description')->where('thing', '[0-9]+');
        Route::put('/things/{thing}/update-description/{description}', [ThingController::class, 'updateDescription'])->name('things.update-description')->where('thing', '[0-9]+');
        // места хранения (ресурсный маршрут)
        Route::resource('places', PlaceController::class)->except(['show']);
        Route::get('/places/{place}', [PlaceController::class, 'show'])->name('places.show');
        // архив удаленных вещей
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
    
    // Дашборд без префикса 
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
    Route::get('/things/{thing}/transfer-form', [ThingController::class, 'transferForm'])->name('things.transfer.form')->where('thing', '[0-9]+');
    Route::post('/things/{thing}/transfer', [ThingController::class, 'transfer'])->name('things.transfer')->where('thing', '[0-9]+');
    Route::post('/things/{thing}/return', [ThingController::class, 'return'])->name('things.return')->where('thing', '[0-9]+');
    // Описания вещей
    Route::post('/things/{thing}/add-description', [ThingController::class, 'addDescription'])->name('things.add-description')->where('thing', '[0-9]+');
    Route::post('/things/{thing}/set-current-description/{description}', [ThingController::class, 'setCurrentDescription'])->name('things.set-current-description')->where('thing', '[0-9]+');
    Route::put('/things/{thing}/update-description/{description}', [ThingController::class, 'updateDescription'])->name('things.update-description')->where('thing', '[0-9]+');
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