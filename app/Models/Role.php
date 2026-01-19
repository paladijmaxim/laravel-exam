<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = 
    [
        'name', 
        'description'
    ];

    public function users(): HasMany // ОДНА роль может иметь МНОГО пользователей
    {
        return $this->hasMany(User::class); // второй параметр не указан, поэтому ларавел будет искать поле т в таблице users
    }
}