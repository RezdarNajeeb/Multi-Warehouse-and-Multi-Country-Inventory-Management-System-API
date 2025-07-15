<?php

namespace App\Repositories;

use App\Models\InventoryTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InventoryTransactionRepository
{
  public function paginate(int $perPage = 10): LengthAwarePaginator
  {
    return InventoryTransaction::with(['product', 'warehouse', 'supplier', 'createdBy'])->paginate($perPage);
  }

  public function create(array $data): InventoryTransaction
  {
    return InventoryTransaction::create($data)->load(['product', 'warehouse', 'supplier', 'createdBy']);
  }
}
