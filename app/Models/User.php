<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function things(): HasMany
    {
        return $this->hasMany(Thing::class, 'master');
    }

    public function usedThings()
    {
        return $this->belongsToMany(Thing::class, 'uses', 'user_id', 'thing_id')
                    ->withPivot('amount', 'place_id', 'unit_id')
                    ->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role && $this->role->name === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role && $this->role->name === 'user';
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('read', false);
    }

    public function unreadNotificationsCount()
    {
        return $this->unreadNotifications()->count();
    }

    public function descriptionNotifications()
    {
        return $this->hasMany(DescriptionNotification::class);
    }

    public function unreadDescriptionNotificationsCount()
    {
        return $this->descriptionNotifications()->where('read', false)->count();
    }
}