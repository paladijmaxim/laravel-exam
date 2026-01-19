<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Thing extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'wrnt',
        'master' // внешний ключ к User
    ];

    protected $casts = [
        'wrnt' => 'date'
    ];

    public function owner(): BelongsTo // Владелец вещи
    {
        return $this->belongsTo(User::class, 'master');
    }
    
    public function usages(): HasMany // Использования вещи
    {
        return $this->hasMany(UseModel::class, 'thing_id');
    }
   
    public function descriptions(): HasMany // Все описания вещи
    {
        return $this->hasMany(Description::class);
    }
   
    public function currentUsage() // Текущее использование (последняя запись)
    {
        return $this->usages()->latest()->first();
    }

    public function currentUser() // если есть текущее использование возвращает пользователя из этой записи
    {
        $usage = $this->currentUsage();
        return $usage ? $usage->user : null;
    }

    public function currentPlace() // Текущее место хранения
    {
        $usage = $this->currentUsage();
        return $usage ? $usage->place : null;
    }
  
    public function isInUse(): bool // Проверка, находится ли вещь в использовании
    {
        return $this->usages()->exists();
    }

    public function isBorrowed(): bool  // Альтернативное название для совместимости
    {
        return $this->isInUse();
    }

    public function isInSpecialPlace(): string
    {
        if (!$this->isInUse()) {
            return '';
        }
        
        $place = $this->currentPlace();
        
        if (!$place) {
            return '';
        }
        
        if ($place->repair) {
            return 'repair';
        }
        
        if ($place->work) {
            return 'work';
        }
        
        return '';
    }

     // Boot method для архивации при удалении
    protected static function booted(): void
    {
        static::deleted(function (Thing $thing) {
            // Получаем данные для архива
            $owner = $thing->owner;
            $currentUsage = $thing->usages()->latest()->first();
            
            ArchivedThing::create([
                'original_id' => $thing->id,
                'name' => $thing->name,
                'description' => $thing->description,
                'wrnt' => $thing->wrnt,
                
                // Информация о владельце
                'owner_name' => $owner ? $owner->name : 'Неизвестно',
                'owner_email' => $owner ? $owner->email : 'Неизвестно',
                
                // Информация о последнем пользователе
                'last_user_name' => $currentUsage && $currentUsage->user ? $currentUsage->user->name : null,
                'last_user_email' => $currentUsage && $currentUsage->user ? $currentUsage->user->email : null,
                
                // Информация о месте хранения
                'place_name' => $currentUsage && $currentUsage->place ? $currentUsage->place->name : null,
                'place_description' => $currentUsage && $currentUsage->place ? $currentUsage->place->description : null,
                
                // Количество и единица измерения
                'amount' => $currentUsage ? $currentUsage->amount : 1,
                'unit_name' => $currentUsage && $currentUsage->unit ? $currentUsage->unit->name : null,
                'unit_abbreviation' => $currentUsage && $currentUsage->unit ? $currentUsage->unit->abbreviation : null,
                
                // Метаданные
                'metadata' => [
                    'created_at' => $thing->created_at->toIso8601String(),
                    'updated_at' => $thing->updated_at->toIso8601String(),
                ],
            ]);
        });

        // При создании вещи создаем первое описание
        static::created(function (Thing $thing) {
            if ($thing->description) {
                $thing->descriptions()->create([
                    'description' => $thing->description,
                    'is_current' => true,
                    'created_by' => $thing->master
                ]);
            }
        });
    }

     // восстановление из архива
    public static function restoreFromArchive(ArchivedThing $archivedThing, User $restorer): Thing
    {
        $thing = Thing::create([
            'name' => $archivedThing->name,
            'description' => $archivedThing->description,
            'wrnt' => $archivedThing->wrnt,
            'master' => $restorer->id,
        ]);
        
        $archivedThing->update([
            'restored' => true,
            'restored_at' => now(),
            'restored_by' => $restorer->id,
            'restored_by_name' => $restorer->name,
        ]);

        return $thing;
    }
}