<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DescriptionNotification extends Model
{
    use HasFactory;

    protected $table = 'description_notifications'; // хранение уведомлений о новых описаниях вещей
    
    protected $fillable = [
        'user_id',
        'thing_id', 
        'from_user_id',
        'type',
        'title',
        'message',
        'read',
        'read_at'
    ];

    protected $casts = [
        'read' => 'boolean',
        'read_at' => 'datetime' // преобразует строку в объект Carbon
    ];

    public function user() // кто получил уведу
    {
        return $this->belongsTo(User::class);  // ищет в таблице description_notifications поле user_id
    }

    // о какой вещи уведа
    public function thing()
    {
        return $this->belongsTo(Thing::class); // ищет в таблице description_notifications поле thing_id
    }

    // кто изменил описание
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }
}