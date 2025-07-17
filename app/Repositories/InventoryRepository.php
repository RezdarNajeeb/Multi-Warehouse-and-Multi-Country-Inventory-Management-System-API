<?php

namespace App\Repositories;

use App\Models\Inventory;
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

  public function getGlobalView(array $filters): Collection
  {
    return Inventory::with('product:id,name,sku')
      ->selectRaw('product_id, SUM(quantity) as total_stock')
      ->filter($filters)
      ->groupBy('product_id')
      ->get();
  }
}
