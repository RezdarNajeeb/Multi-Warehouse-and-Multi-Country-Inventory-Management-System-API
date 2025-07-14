<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryTransactionRequest;
use App\Http\Resources\InventoryTransactionResource;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class InventoryTransactionController extends Controller
{
    public function index()
    {
        return InventoryTransactionResource::collection(InventoryTransaction::paginate(10));
    }

    public function store(InventoryTransactionRequest $request)
    {
        $validated = $request->validated();

       return DB::transaction(function () use ($validated) {
           $inventory = Inventory::where([
               'product_id' => $validated['product_id'],
               'warehouse_id' => $validated['warehouse_id'],
           ])->lockForUpdate()->first(); // we lock the inventory row to prevent race conditions

           if (!$inventory) {
               return response()->json(['error' => 'Inventory not found'], Response::HTTP_NOT_FOUND);
           }

           if ($validated['transaction_type'] === 'in') {
               $inventory->increment('quantity', $validated['quantity']);
           } else {
                if ($inventory->quantity - $validated['quantity'] < $inventory->min_quantity) {
                     return response()->json(['error' => 'Insufficient stock'], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
                $inventory->decrement('quantity', $validated['quantity']);
           }

           $validated['created_by'] = auth()->id();

           return new InventoryTransactionResource(InventoryTransaction::create($validated));
       });
    }

    public function show(InventoryTransaction $inventoryTransactions)
    {
        return new InventoryTransactionResource($inventoryTransactions);
    }
}
