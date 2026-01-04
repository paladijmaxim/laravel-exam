<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UseModelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'thing_id' => \App\Models\Thing::factory(),
            'place_id' => \App\Models\Place::factory(),
            'user_id' => \App\Models\User::factory(),
            'amount' => $this->faker->numberBetween(1, 10),
            'unit_id' => \App\Models\Unit::factory(),
        ];
    }
}