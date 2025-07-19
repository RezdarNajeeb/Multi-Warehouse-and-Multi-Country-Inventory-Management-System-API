<?php

namespace App\Services;

use App\Http\Resources\InventoryResource;
use App\Repositories\InventoryRepository;
use App\Repositories\InventoryTransactionRepository;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Events\LowStockDetected;

class InventoryTransferService
{
    public function __construct(
        protected InventoryRepository $inventories,
        protected InventoryTransactionRepository $inventoryTransactions,
    ) {
        //
    }

    /**
     * Handle inventory transfer between warehouses.
     *
     * @return array [mixed $data, ?string $error, int $status]
     */
    public function transfer(array $validated): array
    {
        return DB::transaction(function () use ($validated) {
            $sourceInventory = $this->inventories
                ->lockAndGet(
                    $validated['product_id'],
                    $validated['source_warehouse_id']
                );

            if ($sourceInventory->quantity - $validated['quantity'] < 0) {
                return [null, 'Insufficient stock in the source warehouse', Response::HTTP_UNPROCESSABLE_ENTITY];
            }

            $destinationInventory = $this->inventories
                ->lockAndGet(
                    $validated['product_id'],
                    $validated['destination_warehouse_id']
                );

            $sourceInventory->decrement('quantity', $validated['quantity']);
            $destinationInventory->increment('quantity', $validated['quantity']);

            if ($sourceInventory->quantity <= $sourceInventory->min_quantity) {
                event(new LowStockDetected(collect([$sourceInventory])));
            }

            // record transactions (out from source, in to destination)
            $commonData = [
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'date' => $validated['date'] ?? now(),
                'created_by' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ];

            $this->inventoryTransactions->create(array_merge($commonData, [
                'warehouse_id' => $validated['source_warehouse_id'],
                'transaction_type' => 'OUT',
            ]));

            $this->inventoryTransactions->create(array_merge($commonData, [
                'warehouse_id' => $validated['destination_warehouse_id'],
                'transaction_type' => 'IN',
            ]));

            return [
                [
                    'source_inventory' => new InventoryResource($sourceInventory->refresh()),
                    'destination_inventory' => new InventoryResource($destinationInventory->refresh()),
                ], null, Response::HTTP_CREATED
            ];
        });
    }
}
