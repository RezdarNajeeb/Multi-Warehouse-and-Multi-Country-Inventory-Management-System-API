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
 *     description="Report for low stock products"
 * )
 */
class LowStockReportController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/reports/low-stock",
     *     summary="Retrieve low stock report",
     *     tags={"Low Stock Report"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/LowStockReportResource")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     * )
     */
    public function __invoke(): JsonResponse
    {
        return $this->successResponse(
            LowStockReportResource::collection((new LowStockReportService)()),
            'Low stock report retrieved successfully'
        );
    }
}
