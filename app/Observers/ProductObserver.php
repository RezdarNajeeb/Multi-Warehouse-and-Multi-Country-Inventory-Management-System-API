<?php

namespace App\Observers;

use App\Support\SafeCache;

class ProductObserver
{

    /**
     * Handle the User "created" or "updated" event.
     */
    public function saved(): void
    {
        SafeCache::flushTag('products');
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(): void
    {
        SafeCache::flushTag('products');
    }
}
