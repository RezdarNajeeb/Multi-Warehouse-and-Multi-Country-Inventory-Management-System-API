<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseRequest;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class WarehouseController extends Controller
{
    use ApiResponse;

    public function __construct(protected WarehouseService $warehouseService)
    {
        //
    }

    public function index(): JsonResponse
    {
        return $this->successResponse(
            WarehouseResource::collection($this->warehouseService->list())
        );
    }

    public function store(WarehouseRequest $request): JsonResponse
    {
        return $this->createdResponse(
            new WarehouseResource($this->warehouseService->create($request))
        );
    }

    public function show(Warehouse $warehouse): JsonResponse
    {
        return $this->successResponse(new WarehouseResource($warehouse));
    }

    public function update(WarehouseRequest $request, Warehouse $warehouse): JsonResponse
    {
        return $this->successResponse(
            new WarehouseResource($this->warehouseService->update($request, $warehouse)),
            'Updated successfully'
        );
    }

    public function destroy(Warehouse $warehouse): Response
    {
        $this->warehouseService->delete($warehouse);
        return $this->deletedResponse();
    }
}
