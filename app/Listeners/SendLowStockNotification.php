<?php

namespace App\Listeners;

use App\Events\LowStockDetected;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LowStockReportNotification;

class SendLowStockNotification
{
    /**
     * Handle the event.
     */
    public function handle(LowStockDetected $event): void
    {
        Notification::route('mail', config('inventory.low_stock_report_email'))
            ->notify(new LowStockReportNotification($event->lowStocks, ['mail']));
    }
}
