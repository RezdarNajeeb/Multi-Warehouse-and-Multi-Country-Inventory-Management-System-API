<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'status',
        'description',
        'price',
    ];

    protected function casts()
    {
        return [
            'status' => 'boolean',
        ];
    }

    // relations
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function suppliers(): HasManyThrough
    {
        return $this->hasManyThrough(
            Supplier::class,
            InventoryTransaction::class,
            'product_id',
            'id',
            'id',
            'supplier_id'
        );
    }
}
