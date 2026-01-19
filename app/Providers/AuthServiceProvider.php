<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Thing;
use App\Models\Place;
use App\Policies\ThingPolicy;
use App\Policies\PlacePolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [ // рега политик
        Thing::class => ThingPolicy::class, 
        Place::class => PlacePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Gate для администратора
        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });

        // Gate для просмотра всех вещей
        Gate::define('view-all-things', function (User $user) {
            return $user->isAdmin();
        });

        // Gate для управления местами хранения
        Gate::define('manage-places', function (User $user) {
            return $user->isAdmin();
        });
    }
}