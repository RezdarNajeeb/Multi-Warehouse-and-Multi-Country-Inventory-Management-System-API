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
 * @OA\Tag(name="Products", description="Product Management")
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
     *     path="/products",
     *     summary="List products",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ProductResource"))
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return $this->successResponse(
            ProductResource::collection($this->productService->list())
        );
    }

    /**
     * @OA\Post(
     *     path="/products",
     *     summary="Create product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProductRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     )
     * )
     */
    public function store(ProductRequest $request): JsonResponse
    {
        return $this->createdResponse(
            new ProductResource($this->productService->create($request->validated()))
        );
    }

    /**
     * @OA\Get(
     *     path="/products/{product}",
     *     summary="Get product",
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
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     )
     * )
     */
    public function show(int $product): JsonResponse
    {
        return $this->successResponse(new ProductResource($this->productService->find($product)));
    }

    /**
     * @OA\Put(
     *     path="/products/{product}",
     *     summary="Update product",
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
     *         description="Updated",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     )
     * )
     */
    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        return $this->successResponse(
            new ProductResource($this->productService->update($request->validated(), $product)),
            'Updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/products/{product}",
     *     summary="Delete product",
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
     *         description="No Content"
     *     )
     * )
     */
    public function destroy(Product $product): Response
    {
        $this->productService->delete($product);
        return $this->deletedResponse();
    }
}
