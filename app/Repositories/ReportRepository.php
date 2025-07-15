<?php

namespace App\Repositories;

use App\Models\Inventory;
use Illuminate\Support\Collection;

class ReportRepository
{
  /**
   * Fetch inventories where quantity is less than or equal to min_quantity,
   * optional filtering by country or warehouse.
   */
  public function lowStock(?int $countryId = null, ?int $warehouseId = null): Collection
  {
    return Inventory::query()
      ->select('id', 'product_id', 'warehouse_id', 'quantity', 'min_quantity')
      ->whereColumn('quantity', '<=', 'min_quantity')
      ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
      ->when($countryId, fn($q) => $q->whereHas('warehouse', fn($w) => $w->where('country_id', $countryId)))
      ->with([
        'product:id,name,sku',
        'product.suppliers:id,name,contact_info',
        'warehouse:id,location,country_id',
        'warehouse.country:id,name',
      ])
      ->get();
  }
}
