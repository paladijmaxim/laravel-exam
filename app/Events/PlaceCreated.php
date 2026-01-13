<?php

namespace App\Events;

use App\Models\Place;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlaceCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $place;
    public $user;

    public $connection = 'sync';
    public $queue = 'sync';
    
    public function __construct(Place $place, User $user)
    {
        $this->place = $place;
        $this->user = $user;
    }

    public function broadcastOn()
    {
        return new Channel('places');
    }

    public function broadcastAs()
    {
        return 'place.created';
    }

    public function broadcastWith()
    {
        return [
            'place_id' => $this->place->id,
            'place_name' => $this->place->name,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'description' => $this->place->description ?: 'Без описания',
            'url' => route('places.show', $this->place),
            'time' => now()->format('H:i'),
            'is_repair' => (bool) $this->place->repair,
            'is_work' => (bool) $this->place->work,
        ];
    }
}