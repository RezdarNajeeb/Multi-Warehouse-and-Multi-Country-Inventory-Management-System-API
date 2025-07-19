<?php

namespace App\Models;

use App\Traits\HasFormattedDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory, HasFormattedDate;

    protected $fillable = [
        'name',
        'contact_info',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'contact_info' => 'array',
        ];
    }

    // relations
    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
