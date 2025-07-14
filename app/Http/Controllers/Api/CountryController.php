<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountryRequest;
use App\Http\Resources\CountryResource;
use App\Models\Country;

class CountryController extends Controller
{
    public function index()
    {
        return CountryResource::collection(Country::paginate(10));
    }

    public function store(CountryRequest $request)
    {
        return new CountryResource(Country::create($request->validated()));
    }

    public function show(Country $country)
    {
        return new CountryResource($country);
    }

    public function update(CountryRequest $request, Country $country)
    {
        $country->update($request->validated());

        return new CountryResource($country);
    }

    public function destroy(Country $country)
    {
        $country->delete();

        return response()->json();
    }
}
