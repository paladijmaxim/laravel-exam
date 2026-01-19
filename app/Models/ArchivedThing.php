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

    // пользователь, который восстановил вещь
    public function restorer()
    {
        return $this->belongsTo(User::class, 'restored_by');
    }

    // оригинальная вещь если существует
    public function originalThing()
    {
        return $this->belongsTo(Thing::class, 'original_id');
    }
// Аксессоры 

    // проверка, можно ли восстановить вещь, accessor: $archivedThing->can_be_restored
    public function canBeRestored(): bool
    {
        return !$this->restored; // можно восстановить если НЕ restored
    }

    // форматированная дата удаления, accessor: $archivedThing->formatted_deleted_at
    public function getFormattedDeletedAtAttribute(): string
    {
        return $this->deleted_at->format('d.m.Y H:i');
    }


    // форматированная дата восстановления, accessor: $archivedThing->formatted_restored_at
    public function getFormattedRestoredAtAttribute(): ?string
    {
        return $this->restored_at ? $this->restored_at->format('d.m.Y H:i') : null; // Если restored_at не null  то форматируем, иначе null
    }

    // форматированное количество, accessor: $archivedThing->formatted_amount
    public function getFormattedAmountAttribute(): string
    {
        if ($this->unit_abbreviation) {
            return $this->amount . ' ' . $this->unit_abbreviation;
        }
        return $this->amount . ' шт.';
    }

    // полное описание места, accessor: $archivedThing->place_full
    public function getPlaceFullAttribute(): string
    {
        $result = $this->place_name ?? 'Не указано';
        if ($this->place_description) {
            $result .= ' (' . $this->place_description . ')';
        }
        return $result;
    }
}