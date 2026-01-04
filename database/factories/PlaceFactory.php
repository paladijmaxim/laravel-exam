<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PlaceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'repair' => $this->faker->boolean(20), // 20% chance
            'work' => $this->faker->boolean(30), // 30% chance
        ];
    }
}