<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryTransferRequest;
use Illuminate\Http\JsonResponse;
use App\Services\InventoryTransferService;
use App\Traits\ApiResponse;

class InventoryTransferController extends Controller
{
    use ApiResponse;

    public function __construct(protected InventoryTransferService $service)
    {
        //
    }

    public function __invoke(InventoryTransferRequest $request): JsonResponse
    {
        [$data, $error, $status] = $this->service->transfer($request);

        if ($error) {
            return $this->errorResponse($error, $status);
        }

        return $this->successResponse($data, 'Transfer successful', $status);
    }
}
