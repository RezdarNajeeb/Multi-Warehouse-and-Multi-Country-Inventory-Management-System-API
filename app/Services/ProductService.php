<?php

namespace App\Services;

use App\Http\Requests\ProductRequest;
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

  public function list(): CursorPaginator
  {
    return $this->repository->paginate();
  }

  public function find(int $productId): Product
  {
    return Cache::rememberForever("products.{$productId}", function () use ($productId) {
      return $this->repository->find($productId);
    });
  }

  public function create(ProductRequest $request): Product
  {
    return $this->repository->create($request->validated());
  }

  public function update(ProductRequest $request, Product $product): Product
  {
    return $this->repository->update($product, $request->validated());
  }

  public function delete(Product $product): void
  {
    $this->repository->delete($product);
  }
}
