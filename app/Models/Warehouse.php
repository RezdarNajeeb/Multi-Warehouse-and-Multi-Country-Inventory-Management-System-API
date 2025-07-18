<?php

namespace App\Models;

use App\Traits\HasFormattedDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory, HasFormattedDate;

    protected $fillable = [
        'name',
        'location',
        'country_id',
    ];

    // relations
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}
