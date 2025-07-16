<?php

namespace App\Events;

use App\Models\Inventory;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockDetected
{
  use Dispatchable, SerializesModels;

  public function __construct(public Inventory $inventory)
  {
    // Event fires when inventory quantity <= min_quantity
  }
}
