<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    public function __construct(protected ProductRepository $products)
    {
        //
    }

    public function list(int $perPage = 10, array $relations = []): CursorPaginator
    {
        $cursor = request('cursor', 'first');
        $cacheKey = "products:paginate:{$perPage}:{$cursor}";

        // Cache paginated product lists for 5 minutes to boost read performance
        return Cache::store('redis')->tags(['products'])->remember($cacheKey, config('cache.ttl'),
            function () use ($perPage, $relations) {
                return $this->products->paginate($perPage, $relations);
            });
    }

    public function find(int $productId, array $relations = []): Product
    {
        $cacheKey = "products:single:{$productId}";

        return Cache::store('redis')->tags(['products'])->remember($cacheKey, config('cache.ttl'),
            function () use ($productId, $relations) {
                return $this->products->find($productId, $relations);
            });
    }

    public function create(array $validated): Product
    {
        return $this->products->create($validated);
    }

    public function update(array $validated, Product $product): Product
    {
        return $this->products->update($product, $validated);
    }

    public function delete(Product $product): void
    {
        $this->products->delete($product);
    }
}
