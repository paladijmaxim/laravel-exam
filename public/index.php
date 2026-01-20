<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true)); // определние константы LARAVEL_START

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) { 
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php'; // подключеине автозагрузчика классов

$app = require_once __DIR__.'/../bootstrap/app.php'; // создание экземпляра приложжения

$kernel = $app->make(Kernel::class); // создание kernel

$response = $kernel->handle(
    $request = Request::capture() // создание объекта http запроса (его обработка)
)->send();

$kernel->terminate($request, $response);
