<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="Product Management"
 * )
 */
class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(protected ProductService $productService)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="List paginated products",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Products retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ProductResource")),
     *             @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta"),
     *        )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function index(): JsonResponse
    {
        return $this->successResponse(
            ProductResource::collection($this->productService->list()),
            'Products retrieved successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProductRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully Created",
     *         @OA\JsonContent(
     *            type="object",
     *            @OA\Property(property="message", type="string", example="Product created successfully"),
     *            @OA\Property(property="data", ref="#/components/schemas/ProductResource"),
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable Content"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function store(ProductRequest $request): JsonResponse
    {
        return $this->createdResponse(
            new ProductResource($this->productService->create($request->validated())),
            'Product created successfully'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/products/{product}",
     *     summary="Get product details",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Product retrieved successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/ProductResource"),
     *        )
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/Not Found"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function show(Product $product): JsonResponse
    {
        return $this->successResponse(new ProductResource($product), 'Product retrieved successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/products/{product}",
     *     summary="Update existing product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProductRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Product updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/ProductResource"),
     *        )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable Content"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/Not Found")
     * )
     */
    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        return $this->successResponse(
            new ProductResource($this->productService->update($request->validated(), $product)),
            'Product updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{product}",
     *     summary="Delete a product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Deleted successfully"
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/Not Found"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function destroy(Product $product): Response
    {
        $this->productService->delete($product);
        return $this->deletedResponse();
    }
}
