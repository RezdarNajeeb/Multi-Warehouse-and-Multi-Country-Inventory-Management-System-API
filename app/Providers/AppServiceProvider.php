<?php

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Avoid registering the Redis-dependent ProductObserver when running the test suite
        if ($this->app->environment('testing')) {
            return;
        }

        Product::observe(ProductObserver::class);
    }
}
