<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseRequest;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;

class WarehouseController extends Controller
{
    public function index()
    {
        return WarehouseResource::collection(Warehouse::paginate(10));
    }

    public function store(WarehouseRequest $request)
    {
        return new WarehouseResource(Warehouse::create($request->validated()));
    }

    public function show(Warehouse $warehouse)
    {
        return new WarehouseResource($warehouse);
    }

    public function update(WarehouseRequest $request, Warehouse $warehouse)
    {
        $warehouse->update($request->validated());

        return new WarehouseResource($warehouse);
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return response()->json();
    }
}
