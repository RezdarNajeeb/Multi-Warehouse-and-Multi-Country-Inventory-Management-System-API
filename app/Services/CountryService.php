<?php

namespace App\Services;

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

    public function create(array $validated): Country
    {
        return $this->repository->create($validated);
    }

    public function update(array $validated, Country $country): Country
    {
        return $this->repository->update($country, $validated);
    }

    public function delete(Country $country): void
    {
        $this->repository->delete($country);
    }
}
