<?php

namespace App\Services;

use App\Models\Supplier;
use App\Repositories\SupplierRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;

class SupplierService
{
  public function __construct(protected SupplierRepository $suppliers)
  {
    //
  }

  public function list(int $perPage = 10, string $relations = ''): CursorPaginator
  {
    return $this->suppliers->paginate($perPage, explode(',', $relations));
  }

  public function create(array $validated): Supplier
  {
    return $this->suppliers->create($validated);
  }

  public function update(array $validated, Supplier $supplier): Supplier
  {
    return $this->suppliers->update($supplier, $validated);
  }

  public function delete(Supplier $supplier): void
  {
    $this->suppliers->delete($supplier);
  }
}
