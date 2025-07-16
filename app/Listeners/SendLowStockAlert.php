<?php

namespace App\Listeners;

use App\Events\LowStockDetected;
use App\Notifications\LowStockNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendLowStockAlert implements ShouldQueue
{
  /**
   * Handle the event.
   */
  public function handle(LowStockDetected $event): void
  {
    $recipient = config('app.low_stock_report_email');

    if (blank($recipient)) {
      return; // no recipient configured
    }

    Notification::route('mail', $recipient)
      ->notify(new LowStockNotification($event->inventory));
  }
}
