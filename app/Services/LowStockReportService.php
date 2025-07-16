<?php

namespace App\Services;

use App\Models\Inventory;
use App\Repositories\LowStockReportRepository;
use Illuminate\Support\Collection;

class LowStockReportService
{
  public function __invoke(): Collection
  {
    $lowStockReport = (new LowStockReportRepository)();

    $lowStockReport->each(function ($lowStock) {
      $lowStock->product->supplier->contact_info = json_decode($lowStock->product->supplier->contact_info);
    });

    return $lowStockReport;
  }
}
