<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index()
    {
        return SupplierResource::collection(Supplier::paginate(10));
    }

    public function store(SupplierRequest $request)
    {
        return new SupplierResource(Supplier::create($request->validated()));
    }

    public function show(Supplier $supplier)
    {
        return new SupplierResource($supplier);
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());

        return new SupplierResource($supplier);
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return response()->json();
    }
}
