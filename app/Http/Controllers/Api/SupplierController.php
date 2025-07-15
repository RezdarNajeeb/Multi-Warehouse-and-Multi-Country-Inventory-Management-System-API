<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use App\Services\SupplierService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SupplierController extends Controller
{
    use ApiResponse;

    public function __construct(protected SupplierService $supplierService)
    {
        //
    }

    public function index(): JsonResponse
    {
        return $this->successResponse(
            SupplierResource::collection($this->supplierService->list())
        );
    }

    public function store(SupplierRequest $request): JsonResponse
    {
        return $this->createdResponse(
            new SupplierResource($this->supplierService->create($request))
        );
    }

    public function show(Supplier $supplier): JsonResponse
    {
        return $this->successResponse(new SupplierResource($supplier));
    }

    public function update(SupplierRequest $request, Supplier $supplier): JsonResponse
    {
        return $this->successResponse(
            new SupplierResource($this->supplierService->update($request, $supplier)),
            'Updated successfully'
        );
    }

    public function destroy(Supplier $supplier): Response
    {
        $this->supplierService->delete($supplier);
        return $this->deletedResponse();
    }
}
