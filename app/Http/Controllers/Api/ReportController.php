<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Services\ReportService;

class ReportController extends Controller
{
    use ApiResponse;

    public function __construct(protected ReportService $service)
    {
        //
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): \Illuminate\Http\JsonResponse
    {
        $rows = $this->service->lowStock(
            $request->integer('country_id'),
            $request->integer('warehouse_id')
        );

        return $this->successResponse($rows);
    }
}
