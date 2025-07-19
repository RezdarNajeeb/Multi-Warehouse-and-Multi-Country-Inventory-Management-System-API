<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\CursorPaginator;

class ProductRepository
{
    public function paginate(int $perPage, array $relations): CursorPaginator
    {
        return Product::with($relations)->orderBy('id')->cursorPaginate($perPage);
    }

    public function find(int $productId, array $relations): Product
    {
        return Product::findOrFail($productId)->load($relations);
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
