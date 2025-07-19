<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
        'min_quantity',
    ];

    protected function casts(): array
    {
        return [
            'total_stock' => 'integer',
        ];
    }

    // scopes
    public function scopeFilter($query, array $filters): Builder
    {
        return $query
            ->when($filters['country_id'] ?? null,
                fn($q, $id) => $q->whereHas('warehouse', fn($wq) => $wq->where('country_id', $id))
            )
            ->when($filters['warehouse_id'] ?? null, fn($q, $id) => $q->where('warehouse_id', $id)
            );
    }

    // relations
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function transactions(): HasMany
    {
        // only transactions for this inventory in the warehouse
        return $this->hasMany(InventoryTransaction::class, 'product_id', 'product_id')
            ->where('warehouse_id', $this->warehouse_id);
    }

}
