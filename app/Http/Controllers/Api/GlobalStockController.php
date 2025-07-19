<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GlobalStockResource;
use App\Services\InventoryService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GlobalStockController extends Controller
{
    use ApiResponse;

    public function __construct(protected InventoryService $inventoryService)
    {
        //
    }

    /**
     * Handle the incoming request.
     */
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
     *             @OA\Property(property="message", type="string", example="Global stock retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/GlobalStockResource"))
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function __invoke(Request $request): JsonResponse
    {
        return $this->successResponse(
            GlobalStockResource::collection(
                $this->inventoryService->getGlobalView(
                    $request->only(['country_id', 'warehouse_id'])
                )
            ),
            'Global stock retrieved successfully'
        );
    }
}
