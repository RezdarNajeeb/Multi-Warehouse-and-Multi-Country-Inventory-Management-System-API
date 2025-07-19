<?php

namespace App\Services;

use App\Events\LowStockDetected;
use App\Models\Inventory;
use App\Repositories\InventoryRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

class InventoryService
{
  public function __construct(protected InventoryRepository $inventories)
  {
    //
  }

  public function list(int $perPage = 10, string $relations = ''): CursorPaginator
  {
    return $this->inventories->paginate($perPage, explode(',', $relations));
  }

  public function create(array $validated): Inventory
  {
    $inventory = $this->inventories->create($validated);

    // Check if the inventory is low stock after creation
    $this->dispatchLowStockEvent($inventory);

    return $inventory;
  }

  public function update(array $validated, Inventory $inventory): array
  {
    $locked = $this->hasStockOrHistory($inventory);

    $data = $locked ? Arr::only($validated, ['quantity', 'min_quantity']) : $validated;

    $updated = $this->inventories->update($inventory, $data);

    // Check if the updated inventory is low stock
    $this->dispatchLowStockEvent($updated);

    $message = $locked
      ? 'Only quantity and min_quantity were updated because stock or history exists.'
      : 'Updated successfully';

    return [$updated, $message];
  }

  public function delete(Inventory $inventory): ?string
  {
    if ($this->hasStockOrHistory($inventory)) {
      return 'Inventory cannot be deleted (stock or history exists).';
    }

    $this->inventories->delete($inventory);
    return null;
  }

  public function getGlobalView(array $filters = []): Collection
  {
    return $this->inventories->getGlobalView($filters);
  }

  private function hasStockOrHistory(Inventory $inventory): bool
  {
    return $inventory->transactions()->exists() || $inventory->quantity > 0;
  }

  private function dispatchLowStockEvent(Inventory $inventory): void
  {
    if ($inventory->quantity <= $inventory->min_quantity) {
      event(new LowStockDetected(collect([$inventory])));
    }
  }
}
