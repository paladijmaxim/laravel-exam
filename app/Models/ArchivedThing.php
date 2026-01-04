<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArchivedThing extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'archived_things';

    protected $fillable = [
        'original_id',
        'name',
        'description',
        'wrnt',
        'owner_name',
        'owner_email',
        'last_user_name',
        'last_user_email',
        'place_name',
        'place_description',
        'restored',
        'restored_at',
        'restored_by',
        'restored_by_name',
        'amount',
        'unit_name',
        'unit_abbreviation',
        'metadata',
    ];

    protected $casts = [
        'wrnt' => 'date',
        'restored' => 'boolean',
        'restored_at' => 'datetime',
        'deleted_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Пользователь, который восстановил вещь
     */
    public function restorer()
    {
        return $this->belongsTo(User::class, 'restored_by');
    }

    /**
     * Оригинальная вещь (если существует)
     */
    public function originalThing()
    {
        return $this->belongsTo(Thing::class, 'original_id');
    }

    /**
     * Проверка, можно ли восстановить вещь
     */
    public function canBeRestored(): bool
    {
        return !$this->restored;
    }

    /**
     * Форматированная дата удаления
     */
    public function getFormattedDeletedAtAttribute(): string
    {
        return $this->deleted_at->format('d.m.Y H:i');
    }

    /**
     * Форматированная дата восстановления
     */
    public function getFormattedRestoredAtAttribute(): ?string
    {
        return $this->restored_at ? $this->restored_at->format('d.m.Y H:i') : null;
    }

    /**
     * Форматированное количество
     */
    public function getFormattedAmountAttribute(): string
    {
        if ($this->unit_abbreviation) {
            return $this->amount . ' ' . $this->unit_abbreviation;
        }
        return $this->amount . ' шт.';
    }

    /**
     * Полное описание места
     */
    public function getPlaceFullAttribute(): string
    {
        $result = $this->place_name ?? 'Не указано';
        if ($this->place_description) {
            $result .= ' (' . $this->place_description . ')';
        }
        return $result;
    }
}