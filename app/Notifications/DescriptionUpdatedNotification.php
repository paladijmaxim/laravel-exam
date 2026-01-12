<?php

namespace App\Notifications;

use App\Models\Thing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DescriptionUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $thing;

    public function __construct(Thing $thing)
    {
        $this->thing = $thing;
    }

    public function via($notifiable)
    {
        return ['database']; // Только уведомление в системе
    }

    public function toDatabase($notifiable)
    {
        return [
            'thing_id' => $this->thing->id,
            'thing_name' => $this->thing->name,
            'message' => 'Обновлено описание вещи: ' . $this->thing->name,
            'url' => route('things.show', $this->thing),
        ];
    }
}