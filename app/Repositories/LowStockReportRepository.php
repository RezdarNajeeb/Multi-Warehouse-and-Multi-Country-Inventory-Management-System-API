<?php

namespace App\Repositories;

use App\Models\Inventory;
use Illuminate\Support\Collection;

class LowStockReportRepository
{
  public function __invoke(): Collection
  {
    return Inventory::with([
      'product:id,name,sku',
      'warehouse:id,location,country_id',
      'warehouse.country:id,name',
    ])
      ->whereColumn('quantity', '<=', 'min_quantity')
      ->get()
      ->map(fn(Inventory $inventory) => [
        'product_id'         => $inventory->product_id,
        'product_name'       => $inventory->product->name ?? null,
        'sku'                => $inventory->product->sku ?? null,
        'quantity'           => $inventory->quantity,
        'min_quantity'       => $inventory->min_quantity,
        'warehouse_location' => $inventory->warehouse->location ?? null,
        'country'            => $inventory->warehouse->country->name ?? null,
      ]);
  }
}
