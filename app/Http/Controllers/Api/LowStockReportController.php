<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Services\LowStockReportService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\LowStockReportResource;

class LowStockReportController extends Controller
{
    use ApiResponse;

    /**
     * Handle the incoming request.
     */
    public function __invoke(): JsonResponse
    {
        return $this->successResponse(
            LowStockReportResource::collection(
                (new LowStockReportService)()
            )
        );
    }
}
