<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountryRequest;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use App\Services\CountryService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CountryController extends Controller
{
    use ApiResponse;

    public function __construct(protected CountryService $countryService)
    {
        //
    }

    public function index(): JsonResponse
    {
        return $this->successResponse(
            CountryResource::collection($this->countryService->list())
        );
    }

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

    public function destroy(Country $country): JsonResponse
    {
        $this->countryService->delete($country);
        return $this->deletedResponse();
    }
}
