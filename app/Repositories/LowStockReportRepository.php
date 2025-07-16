<?php

namespace App\Repositories;

use App\Models\Inventory;
use Illuminate\Support\Collection;


class LowStockReportRepository
{
  public function __invoke(): Collection
  {
    return Inventory::with([
      'product:id,name,sku,supplier_id',
      'product.supplier:id,contact_info',
      'warehouse:id,location,country_id',
      'warehouse.country:id,name',
    ])
      ->whereColumn('quantity', '<=', 'min_quantity')
      ->get();
  }
}
