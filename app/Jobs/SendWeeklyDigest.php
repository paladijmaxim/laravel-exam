<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\WeeklyDigestMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWeeklyDigest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $users = User::all();
        
        foreach ($users as $user) {
            Mail::to($user->email)->queue(new WeeklyDigestMail());
        }
    }
}
