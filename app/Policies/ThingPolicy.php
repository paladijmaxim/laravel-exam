<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Thing;

class ThingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;  // Все могут смотреть список
    }

    public function view(User $user, Thing $thing): bool
    {
        return true;  // Все могут смотреть конкретную вещь
    }

    public function create(User $user): bool
    {
        // Все аутентифицированные пользователи могут создавать, включая админа
        return $user !== null;
    }

    public function update(User $user, Thing $thing): bool
    {
        // Админ ИЛИ владелец могут редактировать
        return $user->isAdmin() || $user->id === $thing->master;
    }

    public function delete(User $user, Thing $thing): bool
    {
        // Админ ИЛИ владелец может удалить вещь
        return $user->isAdmin() || $user->id === $thing->master;
    }

    public function restore(User $user, Thing $thing): bool
    {
        // Только админ может восстанавливать удалённые вещи
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Thing $thing): bool
    {
        // Только админ может принудительно удалять (минуя корзину)
        return $user->isAdmin();
    }

    public function viewAll(User $user): bool
    {
        // Только админ видит "все вещи" (включая удалённые)
        return $user->isAdmin();
    }
    
    // Дополнительные методы для конкретных действий:
    
    public function addDescription(User $user, Thing $thing): bool
    {
        // Админ ИЛИ владелец могут добавлять описание
        return $user->isAdmin() || $user->id === $thing->master;
    }
    
    public function transfer(User $user, Thing $thing): bool
    {
        // Админ ИЛИ владелец могут передавать вещь
        return $user->isAdmin() || $user->id === $thing->master;
    }
    
    public function return(User $user, Thing $thing): bool
    {
        // Админ ИЛИ владелец ИЛИ текущий пользователь вещи может вернуть
        $currentUser = $thing->usages()->latest()->first()->user_id ?? null;
        return $user->isAdmin() || 
               $user->id === $thing->master || 
               $user->id === $currentUser;
    }
}