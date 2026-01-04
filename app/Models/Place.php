<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Place extends Model
{
    use HasFactory;

    // СТРОГО как в задании: id, name, description, repair, work
    protected $fillable = [
        'name',
        'description',
        'repair',
        'work'
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
}