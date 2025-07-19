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

/**
 * @OA\Tag(
 *     name="Warehouses",
 *     description="Warehouse Management"
 * )
 */
class WarehouseController extends Controller
{
    use ApiResponse;

    public function __construct(protected WarehouseService $warehouseService)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/api/warehouses",
     *     summary="List paginated warehouses",
     *     tags={"Warehouses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         required=false,
     *         description="Number of warehouses per page",
     *         @OA\Schema(type="integer", default=10, example=10)
     *     ),
     *     @OA\Parameter(
     *         name="relations",
     *         in="query",
     *         required=false,
     *         description="Related models to include (comma-separated)",
     *         @OA\Schema(type="string", example="country")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Warehouses retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/WarehouseResource")),
     *             @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta"),
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function index(): JsonResponse
    {
        return $this->successResponse(
            WarehouseResource::collection(
                $this->warehouseService->list(
                    request('perPage', 10),
                    request('relations', ''),
                )
            ),
            'Warehouses retrieved successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/warehouses",
     *     summary="Create a new warehouse",
     *     tags={"Warehouses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/WarehouseRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully Created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Warehouse created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/WarehouseResource"),
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable Content"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function store(WarehouseRequest $request): JsonResponse
    {
        return $this->createdResponse(
            new WarehouseResource(
                $this->warehouseService->create($request->validated())
            ),
            'Warehouse created successfully'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/warehouses/{warehouse}",
     *     summary="Get warehouse details",
     *     tags={"Warehouses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="warehouse",
     *         in="path",
     *         required=true,
     *         description="Warehouse ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="relations",
     *         in="query",
     *         required=false,
     *         description="Related models to include (comma-separated)",
     *         @OA\Schema(type="string", example="country")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Warehouse retrieved successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/WarehouseResource"),
     *         )
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/Not Found"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function show(Warehouse $warehouse): JsonResponse
    {
        return $this->successResponse(
            new WarehouseResource($warehouse)
                ->load(request('relations', 'country')),
            'Warehouse retrieved successfully'
        );
    }

    /**
     * @OA\Put(
     *     path="/api/warehouses/{warehouse}",
     *     summary="Update existing warehouse",
     *     tags={"Warehouses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="warehouse",
     *         in="path",
     *         required=true,
     *         description="Warehouse ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/WarehouseRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Warehouse updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/WarehouseResource"),
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable Content"),
     *     @OA\Response(response=404, ref="#/components/responses/Not Found"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function update(WarehouseRequest $request, Warehouse $warehouse): JsonResponse
    {
        return $this->successResponse(
            new WarehouseResource($this->warehouseService->update($request->validated(), $warehouse)),
            'Warehouse updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/warehouses/{warehouse}",
     *     summary="Delete a warehouse",
     *     tags={"Warehouses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="warehouse",
     *         in="path",
     *         required=true,
     *         description="Warehouse ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=204, description="Deleted successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/Not Found"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function destroy(Warehouse $warehouse): Response
    {
        $this->warehouseService->delete($warehouse);
        return $this->deletedResponse();
    }
}
