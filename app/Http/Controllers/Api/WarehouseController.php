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
     *      path="/warehouses",
     *      summary="List warehouses",
     *      tags={"Warehouses"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/WarehouseResource")))
     * )
     */
    public function index(): JsonResponse
    {
        return $this->successResponse(
            WarehouseResource::collection($this->warehouseService->list())
        );
    }

    /**
     * @OA\Post(
     *      path="/warehouses",
     *      summary="Create warehouse",
     *      tags={"Warehouses"},
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/WarehouseRequest")),
     *      @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/WarehouseResource"))
     * )
     */
    public function store(WarehouseRequest $request): JsonResponse
    {
        return $this->createdResponse(
            new WarehouseResource($this->warehouseService->create($request->validated()))
        );
    }

    /**
     * @OA\Get(
     *      path="/warehouses/{warehouse}",
     *      summary="Get warehouse",
     *      tags={"Warehouses"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(name="warehouse", in="path", required=true, description="Warehouse ID", @OA\Schema(type="integer", example=1)),
     *      @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/WarehouseResource"))
     * )
     */
    public function show(Warehouse $warehouse): JsonResponse
    {
        return $this->successResponse(new WarehouseResource($warehouse));
    }

    /**
     * @OA\Put(
     *      path="/warehouses/{warehouse}",
     *      summary="Update warehouse",
     *      tags={"Warehouses"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(name="warehouse", in="path", required=true, description="Warehouse ID", @OA\Schema(type="integer", example=1)),
     *      @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/WarehouseRequest")),
     *      @OA\Response(response=200, description="Updated", @OA\JsonContent(ref="#/components/schemas/WarehouseResource"))
     * )
     */
    public function update(WarehouseRequest $request, Warehouse $warehouse): JsonResponse
    {
        return $this->successResponse(
            new WarehouseResource($this->warehouseService->update($request->validated(), $warehouse)),
            'Updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *      path="/warehouses/{warehouse}",
     *      summary="Delete warehouse",
     *      tags={"Warehouses"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(name="warehouse", in="path", required=true, description="Warehouse ID", @OA\Schema(type="integer", example=1)),
     *      @OA\Response(response=204, description="No Content")
     * )
     */
    public function destroy(Warehouse $warehouse): Response
    {
        $this->warehouseService->delete($warehouse);
        return $this->deletedResponse();
    }
}
