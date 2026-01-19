<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Description extends Model
{
    use HasFactory;

    protected $table = 'thing_descriptions';
    
    protected $fillable = [
        'thing_id',
        'description',
        'is_current',
        'created_by'
    ];

    protected $casts = [
        'is_current' => 'boolean'
    ];

    public function thing()
    {
        return $this->belongsTo(Thing::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
