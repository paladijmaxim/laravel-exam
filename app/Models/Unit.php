<?php
// app/Models/Unit.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'abbreviation'];
    
    // Связь с использованиями
    public function uses()
    {
        return $this->hasMany(UseModel::class);
    }
    
    // Геттер для отображения
    public function getDisplayAttribute()
    {
        return "{$this->name} ({$this->abbreviation})";
    }
}