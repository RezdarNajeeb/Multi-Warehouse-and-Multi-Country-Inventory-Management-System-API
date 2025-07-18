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
            'sku' => fake()->unique()->bothify('SKU-####-???'),
            'status' => fake()->boolean(),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 10, 1000), // 10.00 - 1000.00
        ];
    }
}
