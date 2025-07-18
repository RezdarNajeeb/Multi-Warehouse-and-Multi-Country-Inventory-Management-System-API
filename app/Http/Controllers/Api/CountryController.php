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
 *     description="Country CRUD Endpoints"
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
     *     path="/countries",
     *     summary="List countries",
     *     tags={"Countries"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CountryResource"))
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return $this->successResponse(
            CountryResource::collection($this->countryService->list())
        );
    }

    /**
     * @OA\Post(
     *     path="/countries",
     *     summary="Create a new country",
     *     tags={"Countries"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CountryRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/CountryResource")
     *     )
     * )
     */
    public function store(CountryRequest $request): JsonResponse
    {
        return $this->createdResponse(
            new CountryResource($this->countryService->create($request->validated()))
        );
    }

    /**
     * @OA\Get(
     *     path="/countries/{country}",
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
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/CountryResource")
     *     )
     * )
     */
    public function show(Country $country): JsonResponse
    {
        return $this->successResponse(new CountryResource($country));
    }

    /**
     * @OA\Put(
     *     path="/countries/{country}",
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
     *         description="Updated",
     *         @OA\JsonContent(ref="#/components/schemas/CountryResource")
     *     )
     * )
     */
    public function update(CountryRequest $request, Country $country): JsonResponse
    {
        return $this->successResponse(
            new CountryResource($this->countryService->update($request->validated(), $country)),
            'Updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *     path="/countries/{country}",
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
     *     )
     * )
     */
    public function destroy(Country $country): Response
    {
        $this->countryService->delete($country);
        return $this->deletedResponse();
    }
}
