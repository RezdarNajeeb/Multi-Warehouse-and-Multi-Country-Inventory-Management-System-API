<?php

namespace App\Repositories;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Collection;

class InventoryRepository
{
  public function paginate(int $perPage = 10): CursorPaginator
  {
    return Inventory::with(['product', 'warehouse'])->orderBy('id')->cursorPaginate($perPage);
  }

  public function create(array $data): Inventory
  {
    return Inventory::create($data)->load(['product', 'warehouse']);
  }

  public function update(Inventory $inventory, array $data): Inventory
  {
    $inventory->update($data);
    return $inventory->load(['product', 'warehouse']);
  }

  public function delete(Inventory $inventory): void
  {
    $inventory->delete();
  }

  public function lockForUpdate(int $productId, int $warehouseId): Inventory
  {
    return Inventory::where('product_id', $productId)
      ->where('warehouse_id', $warehouseId)
      ->lockForUpdate()
      ->firstOrFail();
  }

  public function getGlobalView(?int $countryId = null, ?int $warehouseId = null): Collection
  {
    $products = Product::query()
      ->select('id', 'name', 'sku')
      ->when($countryId || $warehouseId, function ($q) use ($countryId, $warehouseId) {
        $q->whereHas('inventories', function ($invQuery) use ($countryId, $warehouseId) {
          if ($countryId) {
            $invQuery->whereHas('warehouse', fn($w) => $w->where('country_id', $countryId));
          }
          if ($warehouseId) {
            $invQuery->where('warehouse_id', $warehouseId);
          }
        });
      })
      ->withSum(['inventories as total_quantity' => function ($invQuery) use ($countryId, $warehouseId) {
        if ($countryId) {
          $invQuery->whereHas('warehouse', fn($w) => $w->where('country_id', $countryId));
        }
        if ($warehouseId) {
          $invQuery->where('warehouse_id', $warehouseId);
        }
      }], 'quantity')
      ->orderBy('name')
      ->get()
      ->map(function ($product) {
        return [
          'product_id'     => $product->id,
          'name'           => $product->name,
          'sku'            => $product->sku,
          'total_quantity' => (int) $product->total_quantity,
        ];
      });

    return $products;
  }
}
