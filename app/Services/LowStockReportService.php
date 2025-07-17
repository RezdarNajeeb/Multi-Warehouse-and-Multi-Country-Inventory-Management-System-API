<?php

namespace App\Services;

use App\Models\Inventory;
use App\Repositories\LowStockReportRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class LowStockReportService
{
  public function __invoke(): Collection
  {
    $lowStockReport = (new LowStockReportRepository)();

    return $lowStockReport;
  }
}
