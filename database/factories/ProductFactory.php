<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Supplier;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'name' => fake()->name(),
            'sku' => fake()->word(),
            'status' => fake()->boolean(),
            'description' => fake()->text(),
            'price' => fake()->randomFloat(2, 1, 1000),
        ];
    }
}
