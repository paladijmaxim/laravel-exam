<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Place;

class PlacePolicy
{
    public function viewAny(User $user): bool // просмотр списка мест 
    {
        return true;
    }

    public function view(User $user, Place $place): bool // просмотр конкретного места
    {
        return true;
    }

    public function create(User $user): bool 
    {
        return $user->isAdmin();
    }

    public function update(User $user, Place $place): bool 
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Place $place): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Place $place): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Place $place): bool
    {
        return $user->isAdmin();
    }
}