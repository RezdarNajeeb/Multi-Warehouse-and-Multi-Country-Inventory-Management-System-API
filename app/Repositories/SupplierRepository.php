<?php

namespace App\Repositories;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\CursorPaginator;

class SupplierRepository
{
  /**
   * Paginate suppliers list.
   */
  public function paginate(int $perPage = 10): CursorPaginator
  {
    return Supplier::orderBy('id')->cursorPaginate($perPage);
  }

  /**
   * Store a new supplier and return the model instance.
   */
  public function create(array $data): Supplier
  {
    return Supplier::create($data);
  }

  /**
   * Update the given supplier with provided data.
   */
  public function update(Supplier $supplier, array $data): Supplier
  {
    $supplier->update($data);

    return $supplier;
  }

  /**
   * Delete the given supplier.
   */
  public function delete(Supplier $supplier): void
  {
    $supplier->delete();
  }
}
