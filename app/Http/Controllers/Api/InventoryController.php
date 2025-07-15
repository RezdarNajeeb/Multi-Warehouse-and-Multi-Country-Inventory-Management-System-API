<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryRequest;
use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use App\Services\InventoryService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InventoryController extends Controller
{
    use ApiResponse;

    public function __construct(protected InventoryService $inventoryService)
    {
        //
    }

    public function index(): JsonResponse
    {
        return $this->successResponse(
            InventoryResource::collection($this->inventoryService->list())
        );
    }

    public function store(InventoryRequest $request): JsonResponse
    {
        return $this->createdResponse(
            new InventoryResource($this->inventoryService->create($request))
        );
    }

    public function show(Inventory $inventory): JsonResponse
    {
        return $this->successResponse(new InventoryResource($inventory->load(['product', 'warehouse'])));
    }

    public function update(InventoryRequest $request, Inventory $inventory): JsonResponse
    {
        [$updated, $message] = $this->inventoryService->update($request, $inventory);

        return $this->successResponse(new InventoryResource($updated), $message);
    }

    public function destroy(Inventory $inventory): Response|JsonResponse
    {
        $error = $this->inventoryService->delete($inventory);
        if ($error) {
            return $this->validationErrorResponse($error);
        }

        return $this->deletedResponse();
    }

    public function getGlobalView(Request $request): JsonResponse
    {
        $data = $this->inventoryService->getGlobalView(
            $request->integer('country_id'),
            $request->integer('warehouse_id')
        );

        return $this->successResponse($data);
    }
}
