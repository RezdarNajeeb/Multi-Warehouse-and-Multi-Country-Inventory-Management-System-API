<?php

namespace App\Services;

use App\Http\Requests\InventoryTransferRequest;
use App\Http\Resources\InventoryResource;
use App\Repositories\InventoryRepository;
use App\Repositories\InventoryTransactionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Events\LowStockDetected;

class InventoryTransferService
{
  public function __construct(
    protected InventoryRepository $inventories,
    protected InventoryTransactionRepository $transactions,
  ) {
    //
  }

  /**
   * Handle inventory transfer between warehouses.
   *
   * @return array [mixed $data, ?string $error, int $status]
   */
  public function transfer(InventoryTransferRequest $request): array
  {
    $validated = $request->validated();

    return DB::transaction(function () use ($validated) {
      // lock source inventory
      $sourceInventory = $this->inventories->lockForUpdate(
        $validated['product_id'],
        $validated['source_warehouse_id']
      );

      // ensure sufficient stock (cannot go below min_quantity)
      if ($sourceInventory->quantity - $validated['quantity'] < $sourceInventory->min_quantity) {
        return [null, 'Insufficient stock in the source warehouse', Response::HTTP_UNPROCESSABLE_ENTITY];
      }

      // lock destination inventory
      $destinationInventory = $this->inventories->lockForUpdate(
        $validated['product_id'],
        $validated['destination_warehouse_id']
      );

      // adjust quantities
      $sourceInventory->decrement('quantity', $validated['quantity']);
      $destinationInventory->increment('quantity', $validated['quantity']);

      if ($sourceInventory->quantity <= $sourceInventory->min_quantity) {
        event(new LowStockDetected(collect([$sourceInventory])));
      }

      // record transactions (out from source, in to destination)
      $commonData = [
        'product_id' => $validated['product_id'],
        'quantity'   => $validated['quantity'],
        'date'       => $validated['date'] ?? now(),
        'created_by' => Auth::id(),
        'notes'      => $validated['notes'] ?? null,
      ];

      $this->transactions->create(array_merge($commonData, [
        'warehouse_id'     => $validated['source_warehouse_id'],
        'transaction_type' => 'out',
      ]));

      $this->transactions->create(array_merge($commonData, [
        'warehouse_id'     => $validated['destination_warehouse_id'],
        'transaction_type' => 'in',
      ]));

      return [[
        'source_inventory'      => new InventoryResource($sourceInventory->refresh()),
        'destination_inventory' => new InventoryResource($destinationInventory->refresh()),
      ], null, Response::HTTP_CREATED];
    });
  }
}
