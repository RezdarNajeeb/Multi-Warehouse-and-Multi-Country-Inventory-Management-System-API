<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryRequest;
use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InventoryController extends Controller
{
    public function index()
    {
        return InventoryResource::collection(Inventory::paginate(10));
    }

    public function store(InventoryRequest $request)
    {
        return new InventoryResource(Inventory::create($request->validated()));
    }

    public function show(Inventory $inventory)
    {
        return new InventoryResource($inventory);
    }

    public function update(InventoryRequest $request, Inventory $inventory)
    {
        if ($inventory->transactions()->exists() || ($inventory->quantity > 0)) {
            $validated = $request->safe()->only(['quantity', 'min_quantity']);

            $inventory->update($validated);

            return response()->json([
                'message' => 'Only (quantity and min_quantity) was updated. Other fields are locked due to existing transactions or stock.',
                'updated_fields' => ['min_quantity', 'quantity'],
                'data' => new InventoryResource($inventory->load(['product', 'warehouse'])),
            ]);
        }

        $inventory->update($request->validated());

        return new InventoryResource($inventory->load(['product', 'warehouse']));
    }


    public function destroy(Inventory $inventory)
    {
        if (
            $inventory->transactions()->exists() ||
            $inventory->quantity > 0
        ) {
            return response()->json(
                ['message' => 'Inventory cannot be deleted (stock or history exists).'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $inventory->delete();

        return response()->json();
    }

    public function getGlobalView(Request $request): JsonResponse
    {
        $query = Inventory::join('products', 'inventories.product_id', '=', 'products.id')
            ->join('warehouses', 'inventories.warehouse_id', '=', 'warehouses.id')
            ->select(
                'products.id as product_id',
                'products.name',
                'products.sku',
                DB::raw('SUM(inventories.quantity) as total_quantity')
            );

        if ($request->filled('country_id')) {
            $query->where('warehouses.country_id', $request->input('country_id'));
        }

        if ($request->filled('warehouse_id')) {
            $query->where('inventories.warehouse_id', $request->input('warehouse_id'));
        }

        $data = $query
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('products.name')
            ->get();

        return response()->json($data);
    }
}
