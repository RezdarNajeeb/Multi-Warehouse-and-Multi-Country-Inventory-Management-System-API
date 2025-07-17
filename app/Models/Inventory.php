<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
        'min_quantity',
    ];

    // relations
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    // unique pair of product and warehouse relationship
//    public function uniqueProductWarehouse(): BelongsTo
////    {
////        return $this->belongsTo(Product::class, 'product_id')
////            ->where('warehouse_id', $this->warehouse_id);
////    }

    public function transactions(): HasMany
    {
        // only transactions for this inventory in the warehouse
        return $this->hasMany(InventoryTransaction::class, 'product_id', 'product_id')
            ->where('warehouse_id', $this->warehouse_id);
    }

}
