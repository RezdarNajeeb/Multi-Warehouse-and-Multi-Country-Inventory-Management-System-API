<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Services\LowStockReportService;
use Illuminate\Http\JsonResponse;

class LowStockReportController extends Controller
{
    use ApiResponse;

    /**
     * Handle the incoming request.
     */
    public function __invoke(): JsonResponse
    {
        return $this->successResponse((new LowStockReportService)());
    }
}
