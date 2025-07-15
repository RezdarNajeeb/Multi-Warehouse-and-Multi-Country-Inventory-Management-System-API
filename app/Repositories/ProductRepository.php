<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository
{
  public function paginate(int $perPage = 10): LengthAwarePaginator
  {
    return Product::paginate($perPage);
  }

  public function create(array $data): Product
  {
    return Product::create($data);
  }

  public function update(Product $product, array $data): Product
  {
    $product->update($data);
    return $product;
  }

  public function delete(Product $product): void
  {
    $product->delete();
  }
}
