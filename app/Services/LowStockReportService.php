<?php

namespace App\Services;

use App\Repositories\LowStockReportRepository;
use Illuminate\Support\Collection;

class LowStockReportService
{
    public function __construct(protected LowStockReportRepository $lowStocksReport)
    {
        //
    }

    public function __invoke(): Collection
  {
      return ($this->lowStocksReport)();
  }
}
