<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendWeeklyDigest;

class SendTestMail extends Command
{
    protected $signature = 'mail:send-test';
    protected $description = 'Отправляет тестовую рассылку';

    public function handle()
    {
        SendWeeklyDigest::dispatch();
        $this->info('Рассылка запущена.');
    }
}