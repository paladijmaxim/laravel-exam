<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Thing;
use App\Models\Place;
use App\Models\UseModel;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // запуск сидеров для ролей и единиц измерения
        $this->call([
            RoleSeeder::class,
            UnitSeeder::class,
            UnitsTableSeeder::class,
        ]);

        // создание администратора
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => 1, // admin
        ]);

        // создание обычных пользователей
        $users = User::factory(5)->create([
            'role_id' => 2, // user
        ]);

        // создание места хранения
        $places = Place::factory(10)->create();

        // создание вещи
        $things = Thing::factory(20)->create([
            'master' => $admin->id,
        ]);

        // создание записи об использовании
        foreach ($things->take(8) as $thing) {
            UseModel::factory()->create([
                'thing_id' => $thing->id,
                'place_id' => $places->random()->id,
                'user_id' => $users->random()->id,
                'unit_id' => rand(1, 6),
            ]);
        }

        // первые 3 после первых восьми в ремонте
        foreach ($things->slice(8, 3) as $thing) {
            $repairPlace = Place::where('repair', true)->first();
            if ($repairPlace) {
                UseModel::factory()->create([
                    'thing_id' => $thing->id,
                    'place_id' => $repairPlace->id, // ид места для ремонта
                    'user_id' => $users->random()->id,
                    'unit_id' => rand(1, 6),
                ]);
            }
        }

        // некторые вещи в работе
        foreach ($things->slice(11, 3) as $thing) {
            $workPlace = Place::where('work', true)->first();
            if ($workPlace) {
                UseModel::factory()->create([
                    'thing_id' => $thing->id,
                    'place_id' => $workPlace->id,
                    'user_id' => $users->random()->id,
                    'unit_id' => rand(1, 6),
                ]);
            }
        }
    }
}