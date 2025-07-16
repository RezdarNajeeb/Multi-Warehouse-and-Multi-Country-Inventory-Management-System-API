<?php

namespace App\Services;

use App\Http\Requests\WarehouseRequest;
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

  public function create(WarehouseRequest $request): Warehouse
  {
    return $this->repository->create($request->validated());
  }

  public function update(WarehouseRequest $request, Warehouse $warehouse): Warehouse
  {
    return $this->repository->update($warehouse, $request->validated());
  }

  public function delete(Warehouse $warehouse): void
  {
    $this->repository->delete($warehouse);
  }
}
