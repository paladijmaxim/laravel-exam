<?php
// database/seeders/UnitsTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitsTableSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Штуки', 'abbreviation' => 'шт'],
            ['name' => 'Килограммы', 'abbreviation' => 'кг'],
            ['name' => 'Граммы', 'abbreviation' => 'г'],
            ['name' => 'Литр', 'abbreviation' => 'л'],
            ['name' => 'Метр', 'abbreviation' => 'м'],
            ['name' => 'Сантиметр', 'abbreviation' => 'см'],
            ['name' => 'Комплект', 'abbreviation' => 'компл'],
            ['name' => 'Упаковка', 'abbreviation' => 'уп'],
            ['name' => 'Пара', 'abbreviation' => 'пар'],
            ['name' => 'Набор', 'abbreviation' => 'наб'],
            ['name' => 'Рулон', 'abbreviation' => 'рул'],
            ['name' => 'Бутылка', 'abbreviation' => 'бут'],
            ['name' => 'Коробка', 'abbreviation' => 'кор'],
            ['name' => 'Мешок', 'abbreviation' => 'меш'],
        ];

        foreach ($units as $unit) {
            DB::table('units')->updateOrInsert(
                ['abbreviation' => $unit['abbreviation']],
                [
                    'name' => $unit['name'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}