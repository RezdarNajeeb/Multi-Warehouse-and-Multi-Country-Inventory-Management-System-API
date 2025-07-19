<?php

namespace App\Services;

use App\Http\Resources\InventoryTransactionResource;
use App\Models\Inventory;
use App\Repositories\InventoryRepository;
use App\Repositories\InventoryTransactionRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Events\LowStockDetected;

class InventoryTransactionService
{
    public function __construct(
        protected InventoryTransactionRepository $inventoryTransactions,
        protected InventoryRepository $inventories
    ) {
        //
    }

    public function list(int $perPage = 10, string $relations = ''): CursorPaginator
    {
        return $this->inventoryTransactions->paginate($perPage, explode(',', $relations));
    }

    public function record(array $validated)
    {
        return DB::transaction(function () use ($validated) {
            $inventory = $this->inventories
                ->lockAndGet(
                    $validated['product_id'],
                    $validated['warehouse_id']
                );

            if ($validated['transaction_type'] === 'in') {
                $inventory->increment('quantity', $validated['quantity']);
            } else {
                if ($inventory->quantity - $validated['quantity'] < 0) {
                    return [null, 'Insufficient stock', Response::HTTP_UNPROCESSABLE_ENTITY];
                }
                $inventory->decrement('quantity', $validated['quantity']);
            }

            if ($inventory->quantity <= $inventory->min_quantity) {
                event(new LowStockDetected(collect([$inventory])));
            }

            $validated['date'] = $validated['date'] ?? now();
            $validated['created_by'] = auth()->id();
            $transaction = $this->inventoryTransactions->create($validated);

            return [new InventoryTransactionResource($transaction), null, Response::HTTP_CREATED];
        });
    }
}
