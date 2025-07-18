<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryRequest;
use App\Http\Resources\InventoryResource;
use App\Http\Resources\GlobalStockResource;
use App\Models\Inventory;
use App\Services\InventoryService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Inventories",
 *     description="Inventory Management"
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
     * @OA\Get(
     *     path="/api/inventories",
     *     summary="List of paginated inventories",
     *     tags={"Inventories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Inventories retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/InventoryResource")),
     *             @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta"),
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function index(): JsonResponse
    {
        return $this->successResponse(
            InventoryResource::collection($this->inventoryService->list()),
            'Inventories retrieved successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/inventories",
     *     summary="Create a new inventory",
     *     tags={"Inventories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/InventoryRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully Created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Inventory created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/InventoryResource"),
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable Content"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function store(InventoryRequest $request): JsonResponse
    {
        return $this->createdResponse(
            new InventoryResource($this->inventoryService->create($request->validated())),
            'Inventory created successfully'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/inventories/{inventory}",
     *     summary="Get inventory details",
     *     tags={"Inventories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="inventory",
     *         in="path",
     *         required=true,
     *         description="Inventory ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Inventory retrieved successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/InventoryResource"),
     *         )
     *     ),
     *     @OA\Response(response=404, description="Inventory not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Inventory not found"))
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function show(Inventory $inventory): JsonResponse
    {
        return $this->successResponse(
            new InventoryResource($inventory->load(['product', 'warehouse'])),
            'Inventory retrieved successfully'
        );
    }

    /**
     * @OA\Put(
     *     path="/api/inventories/{inventory}",
     *     summary="Update existing inventory",
     *     tags={"Inventories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="inventory",
     *         in="path",
     *         required=true,
     *         description="Inventory ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/InventoryRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Inventory updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/InventoryResource"),
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable Content"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, description="Inventory not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Inventory not found"))
     *     )
     * )
     */
    public function update(InventoryRequest $request, Inventory $inventory): JsonResponse
    {
        return $this->successResponse(
            new InventoryResource($this->inventoryService->update($request->validated(), $inventory)),
            'Inventory updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/inventories/{inventory}",
     *     summary="Delete an inventory",
     *     tags={"Inventories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="inventory",
     *         in="path",
     *         required=true,
     *         description="Inventory ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=204, description="Deleted successfully"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, description="Inventory not found",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Inventory not found"))
     *     )
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
     * @OA\Get(
     *     path="/api/inventory/global-view",
     *     summary="Global inventory view",
     *     tags={"Inventories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="country_id",
     *         in="query",
     *         required=false,
     *         description="Filter by country ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="warehouse_id",
     *         in="query",
     *         required=false,
     *         description="Filter by warehouse ID",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Global inventory data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Global inventory view retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/GlobalStockResource"))
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function getGlobalView(Request $request): JsonResponse
    {
        return $this->successResponse(
            GlobalStockResource::collection(
                $this->inventoryService->getGlobalView(
                    $request->only(['country_id', 'warehouse_id'])
                )
            ),
            'Global inventory view retrieved successfully'
        );
    }
}
