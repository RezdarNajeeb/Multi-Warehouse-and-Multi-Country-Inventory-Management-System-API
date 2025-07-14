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
            'quantity' => fake()->numberBetween(1, 100),
            'transaction_type' => fake()->randomElement(['IN', 'OUT']),
            'date' => fake()->dateTimeBetween('-1 year'), // random date between now and 1 year ago
            'notes' => fake()->optional()->sentence(),

            'product_id' => Product::factory(),
            'warehouse_id' => Warehouse::factory(),
            'supplier_id' => Supplier::factory(),
            'created_by' => User::factory(),
        ];
    }
}
