<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryTransactionRequest;
use App\Services\InventoryTransactionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\InventoryTransactionResource;
use App\Models\InventoryTransaction;

/**
 * @OA\Tag(
 *     name="Transactions",
 *     description="Inventory IN / OUT Transactions"
 * )
 */
class InventoryTransactionController extends Controller
{
    use ApiResponse;

    public function __construct(protected InventoryTransactionService $service)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/inventory-transactions",
     *     summary="List all inventory transactions",
     *     tags={"Transactions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of transactions",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/InventoryTransactionResource"))
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return $this->successResponse(
            InventoryTransactionResource::collection($this->service->list())
        );
    }

    /**
     * @OA\Post(
     *     path="/inventory-transactions",
     *     summary="Record a new inventory transaction",
     *     tags={"Transactions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/InventoryTransactionRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Transaction successfully recorded",
     *         @OA\JsonContent(ref="#/components/schemas/InventoryTransactionResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(InventoryTransactionRequest $request): JsonResponse
    {
        [$data, $error, $status] = $this->service->record($request);

        if ($error) {
            return $this->errorResponse($error, $status);
        }

        return $this->createdResponse(new InventoryTransactionResource($data));
    }

    /**
     * @OA\Get(
     *     path="/inventory-transactions/{inventory_transaction}",
     *     summary="Get a specific inventory transaction",
     *     tags={"Transactions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="inventory_transaction",
     *         in="path",
     *         required=true,
     *         description="Transaction ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction details",
     *         @OA\JsonContent(ref="#/components/schemas/InventoryTransactionResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Transaction not found"
     *     )
     * )
     */
    public function show(InventoryTransaction $inventoryTransaction): JsonResponse
    {
        return $this->successResponse(
            new InventoryTransactionResource($inventoryTransaction)
        );
    }
}
