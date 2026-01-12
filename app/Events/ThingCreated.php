<?php

namespace App\Events;

use App\Models\Thing;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThingCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $thing;
    public $user;

    public $connection = 'sync';
    public $queue = 'sync';
    
    public function __construct(Thing $thing, User $user)
    {
        $this->thing = $thing;
        $this->user = $user;
    }

    public function broadcastOn()
    {
        // Канал должен быть публичным
        return new Channel('things');
    }

    public function broadcastAs()
    {
        // Имя события для Pusher
        return 'thing.created';
    }

    public function broadcastWith()
    {
        return [
            'thing_id' => $this->thing->id,
            'thing_name' => $this->thing->name,
            'user_name' => $this->user->name,
            'url' => route('things.show', $this->thing),
            'time' => now()->format('H:i'),
        ];
    }
}