<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

trait HasFormattedDate
{
    public function getCreatedAtAttribute($value): ?string
    {
        return Carbon::parse($value)?->format('M j, Y g:i A');
    }

    public function getUpdatedAtAttribute($value): ?string
    {
        return Carbon::parse($value)?->format('M j, Y g:i A');
    }
}
