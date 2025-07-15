<?php

namespace App\Services;

use App\Models\Inventory;
use App\Repositories\ReportRepository;
use Illuminate\Support\Collection;

class ReportService
{
  public function __construct(protected ReportRepository $reports)
  {
    //
  }

  /**
   * Generate low stock report optionally filtered by country or warehouse.
   */
  public function lowStock(?int $countryId = null, ?int $warehouseId = null): Collection
  {
    return $this->reports->lowStock($countryId, $warehouseId)
      ->map(function (Inventory $inv) {
        return [
          'product_id'             => $inv->product_id,
          'product_name'           => $inv->product->name,
          'sku'                    => $inv->product->sku,
          'current_quantity'       => $inv->quantity,
          'min_required_quantity'  => $inv->min_quantity,
          'warehouse_location'     => $inv->warehouse->location,
          'country'                => $inv->warehouse->country->name ?? null,
          'suppliers'              => $inv->product->suppliers->map(fn($s) => [
            'name'         => $s->name,
            'contact_info' => $s->contact_info,
          ])->values(),
        ];
      });
  }
}
