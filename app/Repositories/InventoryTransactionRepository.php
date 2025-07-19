<?php

namespace App\Repositories;

use App\Models\InventoryTransaction;
use Illuminate\Contracts\Pagination\CursorPaginator;

class InventoryTransactionRepository
{
  public function paginate(int $perPage, array $relations): CursorPaginator
  {
    return InventoryTransaction::with($relations)->orderBy('id')->cursorPaginate($perPage);
  }

  public function create(array $data): InventoryTransaction
  {
    return InventoryTransaction::create($data);
  }
}
