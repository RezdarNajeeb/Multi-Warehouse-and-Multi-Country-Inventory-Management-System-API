<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Services\LowStockReportService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\LowStockReportResource;

/**
 * @OA\Tag(
 *     name="Low Stock Report",
 *     description="Low stock report for all products"
 * )
 */
class LowStockReportController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/reports/low-stock",
     *     summary="Daily low stock report",
     *     tags={"Low Stock Report"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/LowStockReportResource")
     *         )
     *     )
     * )
     */
    public function __invoke(LowStockReportService $service): JsonResponse
    {
        return $this->successResponse(
            LowStockReportResource::collection(
                $service()
            )
        );
    }
}
