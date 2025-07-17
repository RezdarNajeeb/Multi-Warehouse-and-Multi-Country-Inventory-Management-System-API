<?php

namespace App\Services;

use App\Events\LowStockDetected;
use App\Http\Requests\InventoryRequest;
use App\Models\Inventory;
use App\Repositories\InventoryRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Collection;

class InventoryService
{
  public function __construct(protected InventoryRepository $repository)
  {
    //
  }

  public function list(): CursorPaginator
  {
    return $this->repository->paginate();
  }

  public function create(InventoryRequest $request): Inventory
  {
    $inventory = $this->repository->create($request->validated());

    $this->dispatchLowStockEvent($inventory);

    return $inventory;
  }

  /**
   * Update inventory with business constraints.
   * Returns array with [Inventory $model, string $message]
   */
  public function update(InventoryRequest $request, Inventory $inventory): array
  {
    $locked = $this->hasStockOrHistory($inventory);

    $data = $locked ? $request->safe()->only(['quantity', 'min_quantity']) : $request->validated();

    $updated = $this->repository->update($inventory, $data);

    $this->dispatchLowStockEvent($updated);

    $message = $locked
      ? 'Only quantity and min_quantity were updated because stock or history exists.'
      : 'Updated successfully';

    return [$updated, $message];
  }

  /**
   * Attempt to delete inventory. Returns null on success or string error message.
   */
  public function delete(Inventory $inventory): ?string
  {
    if ($this->hasStockOrHistory($inventory)) {
      return 'Inventory cannot be deleted (stock or history exists).';
    }

    $this->repository->delete($inventory);
    return null;
  }

  public function getGlobalView(array $filters): Collection
  {
    return $this->repository->getGlobalView($filters);
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
