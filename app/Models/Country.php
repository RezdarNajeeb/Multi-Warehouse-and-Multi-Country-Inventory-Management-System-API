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

    // relations
    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    // setters
    public function setCodeAttribute(string $value): void
    {
        $this->attributes['code'] = strtoupper($value);
    }
}
