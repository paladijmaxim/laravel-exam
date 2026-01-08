<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'thing_id', 
        'from_user_id',
        'type',
        'title',
        'message',
        'read',
        'read_at'
    ];

    protected $casts = [
        'read' => 'boolean',
        'read_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function thing()
    {
        return $this->belongsTo(Thing::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function markAsRead()
    {
        if (!$this->read) {
            $this->update([
                'read' => true,
                'read_at' => now()
            ]);
        }
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d.m.Y H:i');
    }
}