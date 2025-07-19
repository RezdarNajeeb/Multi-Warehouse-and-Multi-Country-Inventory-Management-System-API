<?php

namespace App\Services;

use App\Models\Warehouse;
use App\Repositories\WarehouseRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;

class WarehouseService
{
  public function __construct(protected WarehouseRepository $warehouses)
  {
    //
  }

  public function list(int $perPage, string $relations = ''): CursorPaginator
  {
    return $this->warehouses->paginate($perPage, explode(',', $relations));
  }

  public function create(array $validated): Warehouse
  {
    return $this->warehouses->create($validated);
  }

  public function update(array $validated, Warehouse $warehouse): Warehouse
  {
    return $this->warehouses->update($warehouse, $validated);
  }

  public function delete(Warehouse $warehouse): void
  {
    $this->warehouses->delete($warehouse);
  }
}
