<?php

namespace App\Repositories;

use App\Models\Country;
use Illuminate\Contracts\Pagination\CursorPaginator;

class CountryRepository
{
    public function paginate(int $perPage): CursorPaginator
    {
        return Country::orderBy('id')->cursorPaginate($perPage);
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
