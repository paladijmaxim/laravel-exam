<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'штуки', 'abbreviation' => 'шт.'],
            ['name' => 'килограммы', 'abbreviation' => 'кг'],
            ['name' => 'граммы', 'abbreviation' => 'г'],
            ['name' => 'литры', 'abbreviation' => 'л'],
            ['name' => 'метры', 'abbreviation' => 'м'],
            ['name' => 'комплекты', 'abbreviation' => 'компл.'],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
