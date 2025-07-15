<?php

namespace App\Repositories;

use App\Models\Warehouse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WarehouseRepository
{
  public function paginate(int $perPage = 10): LengthAwarePaginator
  {
    return Warehouse::with('country')->paginate($perPage);
  }

  public function create(array $data): Warehouse
  {
    return Warehouse::create($data);
  }

  public function update(Warehouse $warehouse, array $data): Warehouse
  {
    $warehouse->update($data);
    return $warehouse->load('country');
  }

  public function delete(Warehouse $warehouse): void
  {
    $warehouse->delete();
  }
}
