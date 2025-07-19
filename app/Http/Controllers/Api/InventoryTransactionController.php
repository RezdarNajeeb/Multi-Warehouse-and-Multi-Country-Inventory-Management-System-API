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
 *     name="Inventory Transactions",
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
     *     path="/api/inventory-transactions",
     *     summary="List of paginated inventory transactions",
     *     tags={"Inventory Transactions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         required=false,
     *         description="Number of transactions per page",
     *         @OA\Schema(type="integer", default=10, example=10)
     *     ),
     *     @OA\Parameter(
     *         name="relations",
     *         in="query",
     *         required=false,
     *         description="Comma-separated list of relations to eager load",
     *         @OA\Schema(type="string", example="product,warehouse,supplier,createdBy")
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Inventory transactions retrieved successfully"),
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/InventoryTransactionResource")),
     *              @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta"),
     *          )
     *      ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     * )
     */
    public function index(): JsonResponse
    {
        return $this->successResponse(
            InventoryTransactionResource::collection(
                $this->service->list(
                    request('perPage', 10),
                    request('relations', '')
                )
            ),
            'Inventory transactions retrieved successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/inventory-transactions",
     *     summary="Record a new inventory transaction",
     *     tags={"Inventory Transactions"},
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
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable Content"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *
     * )
     */
    public function store(InventoryTransactionRequest $request): JsonResponse
    {
        [$data, $error, $status] = $this->service->record($request->validated());

        if ($error) {
            return $this->errorResponse($error, $status);
        }

        return $this->createdResponse(new InventoryTransactionResource($data),
            'Inventory transaction successfully recorded');
    }

    /**
     * @OA\Get(
     *     path="/api/inventory-transactions/{inventory_transaction}",
     *     summary="Get a specific inventory transaction",
     *     tags={"Inventory Transactions"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="inventory_transaction",
     *         in="path",
     *         required=true,
     *         description="Transaction ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="relations",
     *         in="query",
     *         required=false,
     *         description="Comma-separated list of relations to eager load",
     *         @OA\Schema(type="string", example="product,warehouse,supplier,createdBy")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction details",
     *         @OA\JsonContent(ref="#/components/schemas/InventoryTransactionResource")
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/Not Found"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     * )
     */
    public function show(InventoryTransaction $inventoryTransaction): JsonResponse
    {
        return $this->successResponse(
            new InventoryTransactionResource($inventoryTransaction)
                ->load(request('relations', ['product','warehouse','supplier','createdBy'])),
            'Inventory transaction retrieved successfully'
        );
    }
}
