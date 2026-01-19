<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WeeklyDigestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->subject('Еженедельная рассылка о вещах')
                    ->view('emails.weekly-digest');
    }
}
