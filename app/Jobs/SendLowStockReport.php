<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LowStockReportNotification;
use App\Services\LowStockReportService;

class SendLowStockReport implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $lowStocks = (new LowStockReportService)();

        if ($lowStocks->isEmpty()) {
            return;
        }

        Notification::routes([
            'mail' => config('inventory.low_stock_report_email'),
            'slack' => config('services.slack.webhook_url'),
        ])->notify(new LowStockReportNotification($lowStocks));
    }
}
