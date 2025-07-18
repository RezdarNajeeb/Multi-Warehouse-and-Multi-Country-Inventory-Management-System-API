<?php

namespace App\Services;

use App\Models\Warehouse;
use App\Repositories\WarehouseRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;

class WarehouseService
{
  public function __construct(protected WarehouseRepository $repository)
  {
    //
  }

  public function list(): CursorPaginator
  {
    return $this->repository->paginate();
  }

  public function create(array $validated): Warehouse
  {
    return $this->repository->create($validated);
  }

  public function update(array $validated, Warehouse $warehouse): Warehouse
  {
    return $this->repository->update($warehouse, $validated);
  }

  public function delete(Warehouse $warehouse): void
  {
    $this->repository->delete($warehouse);
  }
}
