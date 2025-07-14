<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'sku' => fake()->word(),
            'status' => fake()->boolean(),
            'description' => fake()->text(),
            'price' => fake()->randomFloat(),
        ];
    }
}
