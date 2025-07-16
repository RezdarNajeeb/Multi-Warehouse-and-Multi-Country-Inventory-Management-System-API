<?php

namespace App\Repositories;

use App\Models\InventoryTransaction;
use Illuminate\Contracts\Pagination\CursorPaginator;

class InventoryTransactionRepository
{
  public function paginate(int $perPage = 10): CursorPaginator
  {
    return InventoryTransaction::with(['product', 'warehouse', 'supplier', 'createdBy'])->orderBy('id')->cursorPaginate($perPage);
  }

  public function create(array $data): InventoryTransaction
  {
    return InventoryTransaction::create($data)->load(['product', 'warehouse', 'supplier', 'createdBy']);
  }
}
