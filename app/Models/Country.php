<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    public function getUniqueAttribute(string $value): string
    {
        return strtoupper($value);
    }

    // relations
    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }
}
