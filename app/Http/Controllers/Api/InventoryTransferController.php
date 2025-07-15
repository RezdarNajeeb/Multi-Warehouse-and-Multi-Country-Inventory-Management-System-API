<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryTransferRequest;
use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Http\JsonResponse;

class InventoryTransferController extends Controller
{
    public function __invoke(InventoryTransferRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = DB::transaction(function () use ($validated) {
            $sourceInventory = Inventory::where('product_id', $validated['product_id'])
                ->where('warehouse_id', $validated['source_warehouse_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (($sourceInventory->quantity - $validated['quantity']) < $sourceInventory->min_quantity) {
                throw new HttpResponseException(
                    response()->json([
                        'message' => 'Insufficient stock in the source warehouse.'
                    ], Response::HTTP_UNPROCESSABLE_ENTITY)
                );
            }

            $destinationInventory = Inventory::where('product_id', $validated['product_id'])
                ->where('warehouse_id', $validated['destination_warehouse_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $sourceInventory->decrement('quantity', $validated['quantity']);
            $destinationInventory->increment('quantity', $validated['quantity']);

            InventoryTransaction::create([
                'product_id' => $validated['product_id'],
                'warehouse_id' => $validated['source_warehouse_id'],
                'supplier_id' => null,
                'quantity' => $validated['quantity'],
                'transaction_type' => 'out',
                'date' => $validated['date'] ?? now(),
                'created_by' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            InventoryTransaction::create([
                'product_id' => $validated['product_id'],
                'warehouse_id' => $validated['destination_warehouse_id'],
                'supplier_id' => null,
                'quantity' => $validated['quantity'],
                'transaction_type' => 'in',
                'date' => $validated['date'] ?? now(),
                'created_by' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            return [
                'message' => 'Inventory transfer successful.',
                'source_inventory' => new InventoryResource($sourceInventory->refresh()),
                'destination_inventory' => new InventoryResource($destinationInventory->refresh()),
            ];
        });

        return response()->json($result);
    }
}
