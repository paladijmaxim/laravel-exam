<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Thing;

class ThingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Thing $thing): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return !$user->isAdmin();
    }

    public function update(User $user, Thing $thing): bool
    {
        // ТОЛЬКО админ ИЛИ хозяин (master)
        // Пользователь, на которого назначена вещь (user_id) - НЕ может редактировать
        return $user->isAdmin() || $user->id === $thing->master;
    }

    public function delete(User $user, Thing $thing): bool
    {
        // Только админ или хозяин
        return $user->isAdmin() || $user->id === $thing->master;
    }

    public function restore(User $user, Thing $thing): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Thing $thing): bool
    {
        return $user->isAdmin();
    }

    // Админ может просматривать все вещи
    public function viewAll(User $user): bool
    {
        return $user->isAdmin();
    }
}