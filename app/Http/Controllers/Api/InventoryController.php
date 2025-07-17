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

/**
 * @OA\Tag(
 *     name="Inventories",
 *     description="Inventory Management Endpoints"
 * )
 */
class InventoryController extends Controller
{
    use ApiResponse;

    public function __construct(protected InventoryService $inventoryService)
    {
        //
    }

    /**
     * Get a paginated list of inventories.
     *
     * @OA\Get(
     *     path="/inventories",
     *     summary="List all inventories",
     *     tags={"Inventories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/InventoryResource")
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return $this->successResponse(
            InventoryResource::collection($this->inventoryService->list())
        );
    }

    /**
     * Store a newly created inventory record.
     *
     * @OA\Post(
     *     path="/inventories",
     *     summary="Create inventory",
     *     tags={"Inventories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/InventoryRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Inventory created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/InventoryResource")
     *     )
     * )
     */
    public function store(InventoryRequest $request): JsonResponse
    {
        return $this->createdResponse(
            new InventoryResource($this->inventoryService->create($request))
        );
    }

    /**
     * Show a specific inventory item.
     *
     * @OA\Get(
     *     path="/inventories/{inventory}",
     *     summary="Get single inventory",
     *     tags={"Inventories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="inventory",
     *         in="path",
     *         required=true,
     *         description="Inventory ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inventory fetched",
     *         @OA\JsonContent(ref="#/components/schemas/InventoryResource")
     *     ),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(Inventory $inventory): JsonResponse
    {
        return $this->successResponse(new InventoryResource($inventory->load(['product', 'warehouse'])));
    }

    /**
     * Update a specific inventory item.
     *
     * @OA\Put(
     *     path="/inventories/{inventory}",
     *     summary="Update inventory",
     *     tags={"Inventories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="inventory",
     *         in="path",
     *         required=true,
     *         description="Inventory ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/InventoryRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inventory updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/InventoryResource")
     *     ),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(InventoryRequest $request, Inventory $inventory): JsonResponse
    {
        return $this->successResponse(
            new InventoryResource($this->inventoryService->update($request, $inventory)),
            'Inventory updated successfully'
        );
    }

    /**
     * Delete a specific inventory item.
     *
     * @OA\Delete(
     *     path="/inventories/{inventory}",
     *     summary="Delete inventory",
     *     tags={"Inventories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="inventory",
     *         in="path",
     *         required=true,
     *         description="Inventory ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Deleted successfully"),
     *     @OA\Response(response=422, description="Deletion error")
     * )
     */
    public function destroy(Inventory $inventory): Response|JsonResponse
    {
        $error = $this->inventoryService->delete($inventory);
        if ($error) {
            return $this->validationErrorResponse($error);
        }

        return $this->deletedResponse();
    }

    /**
     * Get a global view of inventory by optional country or warehouse.
     *
     * @OA\Get(
     *     path="/inventory/global-view",
     *     summary="Global inventory view",
     *     tags={"Inventories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="country_id",
     *         in="query",
     *         required=false,
     *         description="Filter by country ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="warehouse_id",
     *         in="query",
     *         required=false,
     *         description="Filter by warehouse ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Global inventory data",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     )
     * )
     */
    public function getGlobalView(Request $request): JsonResponse
    {
        $data = $this->inventoryService->getGlobalView(
            $request->integer('country_id'),
            $request->integer('warehouse_id')
        );

        return $this->successResponse($data);
    }
}
