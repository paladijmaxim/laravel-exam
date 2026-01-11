<?php

namespace App\Mail;

use App\Models\Thing;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ThingDescriptionUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $thing;
    public $user;
    public $description;
    public $isNew;

    public function __construct(Thing $thing, User $user, $description, $isNew = false)
    {
        $this->thing = $thing;
        $this->user = $user;
        $this->description = $description;
        $this->isNew = $isNew;
    }

    public function build()
    {
        $subject = $this->isNew 
            ? "Новое описание для вещи: {$this->thing->name}"
            : "Обновлено описание вещи: {$this->thing->name}";

        return $this->subject($subject)
                    ->markdown('emails.thing-description-updated');
    }
}