<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
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

    // пользователь имеет одну роль
    // ищет в таблице users поле role_id
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // пользователь ИМЕЕТ МНОГО вещей
    // master это внешний ключ в таблице things
    public function things(): HasMany
    {
        return $this->hasMany(Thing::class, 'master');
    }

    // отношение многие-ко-многоим через промежуточную таблицу uses
    public function usedThings()
    {
        return $this->belongsToMany(Thing::class, 'uses', 'user_id', 'thing_id')
                    ->withPivot('amount', 'place_id', 'unit_id') // дополнительные поля из таблицы uses
                    ->withTimestamps(); // автоматически добавляет created_at, updated_at из pivot таблицы
    }

    public function isAdmin(): bool // использование в гейтах
    {
        return $this->role && $this->role->name === 'admin';
    }

    public function isUser(): bool // проверка на обычного юзера
    {
        return $this->role && $this->role->name === 'user';
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class); // ищет в таблице notifications поле user_id
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('read', false); // возвращает QueryBuilder
    }

    public function unreadNotificationsCount()
    {
        return $this->unreadNotifications()->count();
    }

    public function descriptionNotifications(): HasMany
    {
        return $this->hasMany(DescriptionNotification::class);
    }

    public function unreadDescriptionNotificationsCount()
    {
        return $this->descriptionNotifications()->where('read', false)->count();
    }
}