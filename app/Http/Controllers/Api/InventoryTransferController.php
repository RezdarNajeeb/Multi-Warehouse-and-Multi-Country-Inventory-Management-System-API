<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InventoryTransferRequest;
use Illuminate\Http\JsonResponse;
use App\Services\InventoryTransferService;
use App\Traits\ApiResponse;

/**
 * @OA\Tag(
 *     name="Inventory Transfer",
 *     description="Inventory transferring between warehouses"
 * )
 */
class InventoryTransferController extends Controller
{
    use ApiResponse;

    public function __construct(protected InventoryTransferService $service)
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/api/inventory-transfer",
     *     summary="Transfer inventory between warehouses",
     *     tags={"Inventory Transfer"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/InventoryTransferRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Transfer successful",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Transfer successful")
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable Content"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     * )
     */
    public function __invoke(InventoryTransferRequest $request): JsonResponse
    {
        [$data, $error, $status] = $this->service->transfer($request->validated());

        if ($error) {
            return $this->errorResponse($error, $status);
        }

        return $this->createdResponse($data, 'Transfer successful');
    }
}
