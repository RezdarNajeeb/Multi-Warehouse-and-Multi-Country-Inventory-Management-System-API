<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

trait HasFormattedDate
{
    public function getCreatedAtAttribute(string $value): ?string
    {
        return $this->format($value);
    }

    public function getUpdatedAtAttribute(string $value): ?string
    {
        return $this->format($value);
    }

    protected function format(string $value, string $format = 'Y-m-d H:i:s'): ?string
    {
        return Carbon::parse($value)?->format($format);
    }
}
