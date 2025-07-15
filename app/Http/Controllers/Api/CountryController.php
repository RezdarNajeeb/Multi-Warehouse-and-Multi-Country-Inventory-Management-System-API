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
 *     description="API Endpoints for Countries"
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
     *      path="/countries",
     *      operationId="getCountriesList",
     *      tags={"Countries"},
     *      summary="Get list of countries",
     *      description="Returns list of countries",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CountryResource"))
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      )
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
     *      path="/countries",
     *      operationId="storeCountry",
     *      tags={"Countries"},
     *      summary="Store new country",
     *      description="Stores a new country and returns its data",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/CountryRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/CountryResource")
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      )
     * )
     */
    public function store(CountryRequest $request): JsonResponse
    {
        return $this->createdResponse(
            new CountryResource($this->countryService->create($request))
        );
    }

    public function show(Country $country): JsonResponse
    {
        return $this->successResponse(new CountryResource($country));
    }

    public function update(CountryRequest $request, Country $country): JsonResponse
    {
        return $this->successResponse(
            new CountryResource($this->countryService->update($request, $country)),
            'Updated successfully'
        );
    }

    public function destroy(Country $country): Response
    {
        $this->countryService->delete($country);
        return $this->deletedResponse();
    }
}
