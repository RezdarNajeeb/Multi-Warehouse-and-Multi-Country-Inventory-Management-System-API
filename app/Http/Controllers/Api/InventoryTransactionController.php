<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryTransactionRequest;
use App\Services\InventoryTransactionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\InventoryTransactionResource;
use App\Models\InventoryTransaction;

class InventoryTransactionController extends Controller
{
    use ApiResponse;

    public function __construct(protected InventoryTransactionService $service)
    {
        //
    }

    public function index(): JsonResponse
    {
        return $this->successResponse(
            InventoryTransactionResource::collection($this->service->list())
        );
    }

    public function store(InventoryTransactionRequest $request): JsonResponse
    {
        [$data, $error, $status] = $this->service->record($request);

        if ($error) {
            return $this->errorResponse($error, $status);
        }

        return $this->successResponse($data, 'Transaction recorded', $status);
    }

    public function show(InventoryTransaction $inventoryTransaction): JsonResponse
    {
        return $this->successResponse(new InventoryTransactionResource($inventoryTransaction));
    }
}
