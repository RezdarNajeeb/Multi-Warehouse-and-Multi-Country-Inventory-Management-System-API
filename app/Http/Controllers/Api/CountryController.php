<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountryRequest;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use App\Services\CountryService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Countries",
 *     description="Country Management"
 * )
 */
class CountryController extends Controller
{
    use ApiResponse;

    public function __construct(protected CountryService $countryService)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/api/countries",
     *     summary="List paginated countries",
     *     tags={"Countries"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Countries retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CountryResource")),
     *             @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta"),
     *        )
     *     ),
     *
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function index(): JsonResponse
    {
        return $this->successResponse(
            CountryResource::collection(
                $this->countryService->list(
                    request('perPage', 10)
                )
            ),
            'Countries retrieved successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/countries",
     *     summary="Create a new country",
     *     tags={"Countries"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CountryRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully Created",
     *         @OA\JsonContent(
     *            type="object",
     *            @OA\Property(property="message", type="string", example="Country created successfully"),
     *            @OA\Property(property="data", ref="#/components/schemas/CountryResource"),
     *         )
     *     ),
     *
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable Content"),
     *
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function store(CountryRequest $request): JsonResponse
    {
        return $this->createdResponse(
            new CountryResource(
                $this->countryService->create(
                    $request->validated()
                )
            ),
            'Country created successfully'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/countries/{country}",
     *     summary="Get country details",
     *     tags={"Countries"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="country",
     *         in="path",
     *         required=true,
     *         description="Country ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Country retrieved successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/CountryResource"),
     *        )
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/Not Found"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function show(Country $country): JsonResponse
    {
        return $this->successResponse(
            new CountryResource($country),
            'Country retrieved successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/countries/{country}",
     *     summary="Update existing country",
     *     tags={"Countries"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="country",
     *         in="path",
     *         required=true,
     *         description="Country ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CountryRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Country updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/CountryResource"),
     *        )
     *     ),
     *
     *     @OA\Response(response=422, ref="#/components/responses/Unprocessable Content"),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/Not Found"),
     * )
     */
    public function update(CountryRequest $request, Country $country): JsonResponse
    {
        return $this->successResponse(
            new CountryResource(
                $this->countryService->update(
                    $request->validated(), $country
                )
            ),
            'Country updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/countries/{country}",
     *     summary="Delete a country",
     *     tags={"Countries"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="country",
     *         in="path",
     *         required=true,
     *         description="Country ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Deleted successfully"
     *     ),
     *    @OA\Response(response=404, ref="#/components/responses/Not Found"),
     *    @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     * )
     */
    public function destroy(Country $country): Response
    {
        $this->countryService->delete($country);
        return $this->deletedResponse();
    }
}
