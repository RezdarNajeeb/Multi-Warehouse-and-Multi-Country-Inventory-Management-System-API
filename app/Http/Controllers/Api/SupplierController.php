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
 *     description="Supplier Management"
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
     *     path="/api/suppliers",
     *     summary="List paginated suppliers",
     *     tags={"Suppliers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         required=false,
     *         description="Number of suppliers per page",
     *         @OA\Schema(type="integer", default=10, example=10)
     *     ),
     *     @OA\Parameter(
     *         name="relations",
     *         in="query",
     *         required=false,
     *         description="Related models to include (comma-separated)",
     *         @OA\Schema(type="string", example="products")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Suppliers retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/SupplierResource")),
     *             @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta"),
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function index(): JsonResponse
    {
        return $this->successResponse(
            SupplierResource::collection(
                $this->supplierService->list(
                    request('perPage', 10),
                    request('relations', ''),
                )
            ),
            'Suppliers retrieved successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/suppliers",
     *     summary="Create a new supplier",
     *     tags={"Suppliers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SupplierRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully Created",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Supplier created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/SupplierResource"),
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable Content"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function store(SupplierRequest $request): JsonResponse
    {
        return $this->createdResponse(
            new SupplierResource(
                $this->supplierService->create($request->validated())
            ),
            'Supplier created successfully'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/suppliers/{supplier}",
     *     summary="Get supplier details",
     *     tags={"Suppliers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="supplier",
     *         in="path",
     *         required=true,
     *         description="Supplier ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="relations",
     *         in="query",
     *         required=false,
     *         description="Related models to include (comma-separated)",
     *         @OA\Schema(type="string", example="products")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Supplier retrieved successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/SupplierResource"),
     *         )
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/Not Found"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function show(Supplier $supplier): JsonResponse
    {
        return $this->successResponse(
            new SupplierResource($supplier)
                ->load(request('relations', 'products')),
            'Supplier retrieved successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/suppliers/{supplier}",
     *     summary="Update existing supplier",
     *     tags={"Suppliers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="supplier",
     *         in="path",
     *         required=true,
     *         description="Supplier ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SupplierRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Supplier updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/SupplierResource"),
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable Content"),
     *     @OA\Response(response=404, ref="#/components/responses/Not Found"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function update(SupplierRequest $request, Supplier $supplier): JsonResponse
    {
        return $this->successResponse(
            new SupplierResource($this->supplierService->update($request->validated(), $supplier)),
            'Supplier updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/suppliers/{supplier}",
     *     summary="Delete a supplier",
     *     tags={"Suppliers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="supplier",
     *         in="path",
     *         required=true,
     *         description="Supplier ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=204, description="Deleted successfully"),
     *     @OA\Response(response=404, ref="#/components/responses/Not Found"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function destroy(Supplier $supplier): Response
    {
        $this->supplierService->delete($supplier);
        return $this->deletedResponse();
    }
}
