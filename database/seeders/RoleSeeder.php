<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'description' => 'Администратор'],
            ['name' => 'user', 'description' => 'Обычный пользователь'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
