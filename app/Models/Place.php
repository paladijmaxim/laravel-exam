<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Place extends Model
{
    use HasFactory;

    // СТРОГО как в задании: id, name, description, repair, work
    protected $fillable = [
        'name',
        'description',
        'repair',
        'work',
        'created_by' // Добавляем поле
    ];

    protected $casts = [
        'repair' => 'boolean',
        'work' => 'boolean'
    ];

    // Использования в этом месте
    public function usages(): HasMany
    {
        return $this->hasMany(UseModel::class, 'place_id');
    }

    // Кто создал это место
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isAvailable(): bool
    {
        // Место доступно, если оно не на ремонте и не в работе
        return !$this->repair && !$this->work;
    }
}