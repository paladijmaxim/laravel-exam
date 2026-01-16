<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(), // название (штуки, килограммы)
            'abbreviation' => $this->faker->lexify('???'), // сокращение из 3 букв
        ];
    }
}