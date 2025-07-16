<?php

namespace App\Services;

use App\Http\Requests\CountryRequest;
use App\Models\Country;
use App\Repositories\CountryRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;

class CountryService
{
    public function __construct(protected CountryRepository $repository)
    {
        //
    }

    public function list(): CursorPaginator
    {
        return $this->repository->paginate();
    }

    public function create(CountryRequest $request): Country
    {
        return $this->repository->create($request->validated());
    }

    public function update(CountryRequest $request, Country $country): Country
    {
        return $this->repository->update($country, $request->validated());
    }

    public function delete(Country $country): void
    {
        $this->repository->delete($country);
    }
}
