<?php

namespace App\Observers;
 
use Illuminate\Support\Facades\Cache;
 
class ProductObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(): void
    {
        Cache::store('redis')->tags(['products'])->flush();
    }
 
    /**
     * Handle the User "updated" event.
     */
    public function updated(): void
    {
        Cache::store('redis')->tags(['products'])->flush();
    }
 
    /**
     * Handle the User "deleted" event.
     */
    public function deleted(): void
    {
        Cache::store('redis')->tags(['products'])->flush();
    }
}