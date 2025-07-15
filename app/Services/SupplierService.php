<?php

namespace App\Services;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Repositories\SupplierRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupplierService
{
  public function __construct(protected SupplierRepository $repository)
  {
    //
  }

  public function list(int $perPage = 10): LengthAwarePaginator
  {
    return $this->repository->paginate($perPage);
  }

  public function create(SupplierRequest $request): Supplier
  {
    return $this->repository->create($request->validated());
  }

  public function update(SupplierRequest $request, Supplier $supplier): Supplier
  {
    return $this->repository->update($supplier, $request->validated());
  }

  public function delete(Supplier $supplier): void
  {
    $this->repository->delete($supplier);
  }
}
