<?php

namespace App\Services;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
  public function __construct(protected ProductRepository $repository)
  {
    //
  }

  public function list(): LengthAwarePaginator
  {
    return $this->repository->paginate();
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
