<?php

namespace App\Services;

use App\Models\Supplier;
use App\Repositories\SupplierRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;

class SupplierService
{
  public function __construct(protected SupplierRepository $repository)
  {
    //
  }

  public function list(int $perPage = 10, string $relations = ''): CursorPaginator
  {
    return $this->repository->paginate($perPage, explode(',', $relations));
  }

  public function create(array $validated): Supplier
  {
    return $this->repository->create($validated);
  }

  public function update(array $validated, Supplier $supplier): Supplier
  {
    return $this->repository->update($supplier, $validated);
  }

  public function delete(Supplier $supplier): void
  {
    $this->repository->delete($supplier);
  }
}
