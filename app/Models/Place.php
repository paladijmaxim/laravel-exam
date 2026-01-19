<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Place extends Model
{
    use HasFactory; // HasFactory позволяет использовать Place::factory() для создания тестовых данных

    protected $fillable = [
        'name',
        'description',
        'repair',
        'work',
        'created_by' 
    ];

    protected $casts = [
        'repair' => 'boolean',
        'work' => 'boolean'
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(UseModel::class, 'place_id'); // place_id внешний ключ в таблице uses, который ссылается на id в places
    }

    // кто создал это место
    public function creator(): BelongsTo // отношение "принадлежит" возвращает пользователя, который создал это место
    {
        return $this->belongsTo(User::class, 'created_by');  // User::class - связанная модель (User), created_by  поле в таблице places, которое хранит id пользователя
    }

    public function isAvailable(): bool // проверка доступности места 
    {
        // Место доступно, если оно не на ремонте и не в работе
        return !$this->repair && !$this->work;
    }
}