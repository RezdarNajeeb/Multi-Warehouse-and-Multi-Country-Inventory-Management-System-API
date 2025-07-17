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

/**
 * @OA\Tag(
 *     name="Suppliers",
 *     description="Supplier CRUD Endpoints"
 * )
 */
class SupplierController extends Controller
{
    use ApiResponse;

    public function __construct(protected SupplierService $supplierService)
    {
        //
    }

    /**
     * @OA\Get(
     *      path="/suppliers",
     *      summary="List suppliers",
     *      tags={"Suppliers"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/SupplierResource")))
     * )
     */
    public function index(): JsonResponse
    {
        return $this->successResponse(
            SupplierResource::collection($this->supplierService->list())
        );
    }

    /**
     * @OA\Post(
     *      path="/suppliers",
     *      summary="Create supplier",
     *      tags={"Suppliers"},
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/SupplierRequest")),
     *      @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/SupplierResource"))
     * )
     */
    public function store(SupplierRequest $request): JsonResponse
    {
        return $this->createdResponse(
            new SupplierResource($this->supplierService->create($request))
        );
    }

    /**
     * @OA\Get(
     *      path="/suppliers/{supplier}",
     *      summary="Get supplier",
     *      tags={"Suppliers"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(name="supplier", in="path", required=true, description="Supplier ID", @OA\Schema(type="integer", example=1)),
     *      @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/SupplierResource"))
     * )
     */
    public function show(Supplier $supplier): JsonResponse
    {
        return $this->successResponse(new SupplierResource($supplier));
    }

    /**
     * @OA\Put(
     *      path="/suppliers/{supplier}",
     *      summary="Update supplier",
     *      tags={"Suppliers"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(name="supplier", in="path", required=true, description="Supplier ID", @OA\Schema(type="integer", example=1)),
     *      @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/SupplierRequest")),
     *      @OA\Response(response=200, description="Updated", @OA\JsonContent(ref="#/components/schemas/SupplierResource"))
     * )
     */
    public function update(SupplierRequest $request, Supplier $supplier): JsonResponse
    {
        return $this->successResponse(
            new SupplierResource($this->supplierService->update($request, $supplier)),
            'Updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *      path="/suppliers/{supplier}",
     *      summary="Delete supplier",
     *      tags={"Suppliers"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(name="supplier", in="path", required=true, description="Supplier ID", @OA\Schema(type="integer", example=1)),
     *      @OA\Response(response=204, description="No Content")
     * )
     */
    public function destroy(Supplier $supplier): Response
    {
        $this->supplierService->delete($supplier);
        return $this->deletedResponse();
    }
}
