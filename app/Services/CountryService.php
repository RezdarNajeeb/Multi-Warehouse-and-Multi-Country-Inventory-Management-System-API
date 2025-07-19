<?php

namespace App\Services;

use App\Models\Country;
use App\Repositories\CountryRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;

class CountryService
{
    public function __construct(protected CountryRepository $countries)
    {
        //
    }

    public function list(int $perPage = 10): CursorPaginator
    {
        return $this->countries->paginate($perPage);
    }

    public function create(array $validated): Country
    {
        return $this->countries->create($validated);
    }

    public function update(array $validated, Country $country): Country
    {
        return $this->countries->update($country, $validated);
    }

    public function delete(Country $country): void
    {
        $this->countries->delete($country);
    }
}
