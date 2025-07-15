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

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(protected ProductService $productService)
    {
        //
    }

    public function index(): JsonResponse
    {
        return $this->successResponse(
            ProductResource::collection($this->productService->list())
        );
    }

    public function store(ProductRequest $request): JsonResponse
    {
        return $this->createdResponse(
            new ProductResource($this->productService->create($request))
        );
    }

    public function show(Product $product): JsonResponse
    {
        return $this->successResponse(new ProductResource($product));
    }

    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        return $this->successResponse(
            new ProductResource($this->productService->update($request, $product)),
            'Updated successfully'
        );
    }

    public function destroy(Product $product): Response
    {
        $this->productService->delete($product);
        return $this->deletedResponse();
    }
}
