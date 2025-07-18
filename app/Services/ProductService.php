<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Cache;

class ProductService
{
  public function __construct(protected ProductRepository $repository)
  {
    //
  }

  public function list(int $perPage = 10): CursorPaginator
  {
    $cursor = request('cursor', 'first');
    $cacheKey = "products:paginate:{$perPage}:{$cursor}";

    // Cache paginated product lists for 5 minutes to boost read performance
    return Cache::store('redis')->tags(['products'])->remember($cacheKey, config('cache.ttl'), function () use ($perPage) {
      return $this->repository->paginate($perPage);
    });
  }

  public function find(int $productId): Product
  {
    $cacheKey = "products:single:{$productId}";

    return Cache::store('redis')->tags(['products'])->remember($cacheKey, config('cache.ttl'), function () use ($productId) {
      return $this->repository->find($productId);
    });
  }

  public function create(array $validated): Product
  {
    return $this->repository->create($validated);
  }

  public function update(array $validated, Product $product): Product
  {
    return $this->repository->update($product, $validated);
  }

  public function delete(Product $product): void
  {
    $this->repository->delete($product);
  }
}
