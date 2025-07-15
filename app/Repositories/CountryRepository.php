<?php

namespace App\Repositories;

use App\Models\Country;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CountryRepository
{
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Country::paginate($perPage);
    }

    public function create(array $data): Country
    {
        return Country::create($data);
    }

    public function update(Country $country, array $data): Country
    {
        $country->update($data);
        return $country;
    }

    public function delete(Country $country): void
    {
        $country->delete();
    }
}
