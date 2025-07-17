<?php

namespace App\Listeners;

use App\Events\LowStockDetected;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LowStockReport;

class SendLowStockNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LowStockDetected $event): void
    {
        Notification::route('mail', config('inventory.low_stock_report_email'))
            ->notify(new LowStockReport($event->lowStocks, ['mail']));
    }
}
