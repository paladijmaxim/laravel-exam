<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UseModel extends Model
{
    use HasFactory;

    protected $table = 'uses';

    protected $fillable = [
        'thing_id',
        'place_id', 
        'user_id',
        'amount',
        'unit_id'
    ];

    // для составного ключа
    public $incrementing = false;
    protected $primaryKey = null;

    // переопределение save, update, delete для работы с составным ключом
    protected function setKeysForSaveQuery($query)
    {
        $query
            ->where('thing_id', '=', $this->getAttribute('thing_id'))
            ->where('place_id', '=', $this->getAttribute('place_id'))
            ->where('user_id', '=', $this->getAttribute('user_id'));
        
        return $query; // нахождение записи в бд сразу по трем полям одновременно 
    }

    public function thing(): BelongsTo
    {
        return $this->belongsTo(Thing::class); // ищет в таблице uses поле thing_id
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);  // ищет в таблице uses поле place_id
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);  // ищет в таблице uses поле user_id
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);  // ищет в таблице uses поле unit_id
    }

    public function getFormattedAmountAttribute(): string
    {
        if ($this->unit) {
            return $this->amount . ' ' . $this->unit->abbreviation;
        }
        return $this->amount . ' шт.';
    }
}