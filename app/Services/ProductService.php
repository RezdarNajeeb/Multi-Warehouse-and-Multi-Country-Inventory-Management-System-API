<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Support\SafeCache;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    const int CACHE_TTL = 300; // 5 minutes

    public function __construct(protected ProductRepository $products)
    {
        //
    }

    public function list(int $perPage = 10, string $relations = ''): CursorPaginator
    {
        $cursor = request('cursor', 'first');
        $cacheKey = "products:paginate:{$perPage}:{$cursor}";

        // Cache paginated product lists for 5 minutes to boost read performance
        return SafeCache::remember($cacheKey, self::CACHE_TTL, function () use ($perPage, $relations) {
            return $this->products->paginate($perPage, $relations ? explode(',', $relations) : []);
        });
    }

    public function find(int $productId, string $relations = ''): Product
    {
        $cacheKey = "products:single:{$productId}";

        return SafeCache::remember($cacheKey, self::CACHE_TTL, function () use ($productId, $relations) {
            return $this->products->find($productId, $relations ? explode(',', $relations) : []);
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
