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
        'master'
    ];

    protected $casts = [
        'wrnt' => 'date'
    ];

    // Владелец вещи
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'master');
    }

    // Использования вещи
    public function usages(): HasMany
    {
        return $this->hasMany(UseModel::class, 'thing_id');
    }

    // Текущее использование (последняя запись)
    public function currentUsage()
    {
        return $this->usages()->latest()->first();
    }

    // Текущий пользователь вещи
    public function currentUser()
    {
        $usage = $this->currentUsage();
        return $usage ? $usage->user : null;
    }

    // Текущее место хранения
    public function currentPlace()
    {
        $usage = $this->currentUsage();
        return $usage ? $usage->place : null;
    }

    // Проверка, находится ли вещь в использовании
    public function isInUse(): bool
    {
        return $this->usages()->exists();
    }

    // Альтернативное название для совместимости
    public function isBorrowed(): bool
    {
        return $this->isInUse();
    }

    /**
     * Boot method для архивации при удалении
     */
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
                
                'deleted_at' => now(),
            ]);
        });
    }

    /**
     * Архивация вещи (старый метод для совместимости)
     */
    public static function archiveThing(Thing $thing): void
    {
        // Теперь это делается в booted методе
    }

    /**
     * Восстановление из архива
     */
    public static function restoreFromArchive(ArchivedThing $archivedThing, User $restorer): Thing
    {
        // Создаем новую вещь
        $thing = Thing::create([
            'name' => $archivedThing->name,
            'description' => $archivedThing->description,
            'wrnt' => $archivedThing->wrnt,
            'master' => $restorer->id, // Хозяин - тот, кто восстановил
        ]);

        // Обновляем запись в архиве
        $archivedThing->update([
            'restored' => true,
            'restored_at' => now(),
            'restored_by' => $restorer->id,
            'restored_by_name' => $restorer->name,
        ]);

        return $thing;
    }
}