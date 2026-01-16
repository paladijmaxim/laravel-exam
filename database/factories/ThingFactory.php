<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ThingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'wrnt' => $this->faker->dateTimeBetween('now', '+2 years'), // гарантия от тек даты до +2 лет
            'master' => \App\Models\User::factory(), // автоматически создали пользователя
        ];
    }
}