<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\CursorPaginator;

class ProductRepository
{
  public function paginate(int $perPage = 10): CursorPaginator
  {
    return Product::orderBy('id')->cursorPaginate($perPage);
  }

  public function find(int $productId): Product
  {
    return Product::findOrFail($productId);
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
