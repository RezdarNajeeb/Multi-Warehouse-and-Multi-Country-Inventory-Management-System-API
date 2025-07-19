<?php

namespace App\Services;

use App\Repositories\LowStockReportRepository;
use Illuminate\Support\Collection;

class LowStockReportService
{
    public function __invoke(): Collection
  {
      return (new LowStockReportRepository)();
  }
}
