<?php

namespace Database\Factories;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryTransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'warehouse_id' => Warehouse::factory(),
            'supplier_id' => Supplier::factory(),
            'quantity' => fake()->numberBetween(1, 100),
            'transaction_type' => fake()->randomElement(['IN', 'OUT']),
            'date' => fake()->dateTimeThisMonth(),
            'notes' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }

    public function inTransaction(): static
    {
        return $this->state(fn() => [
            'transaction_type' => 'IN',
            'quantity' => $this->faker->numberBetween(10, 100),
        ]);
    }

    public function outTransaction(): static
    {
        return $this->state(fn() => [
            'transaction_type' => 'OUT',
            'quantity' => $this->faker->numberBetween(1, 50),
        ]);
    }
}
