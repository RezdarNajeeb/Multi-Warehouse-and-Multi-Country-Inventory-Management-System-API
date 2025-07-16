<?php

namespace App\Console\Commands;

use App\Mail\LowStockReport;
use App\Models\Inventory;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use App\Notifications\LowStockSlackReport;
use Illuminate\Support\Facades\Notification;

class CheckLowStock extends Command
{
    protected $signature = 'inventory:check-low-stock';
    protected $description = 'Send a low‑stock report when quantity has reached the minimum.';

    public function handle(): int
    {
        $recipient = config('app.low_stock_report_email');

        if (blank($recipient)) {
            $this->error('LOW_STOCK_REPORT_EMAIL is not configured.');
            return self::FAILURE;
        }

        $rows = new Collection();

        Inventory::whereColumn('quantity', '<=', 'min_quantity')
            ->with(['product.suppliers', 'warehouse.country'])
            ->chunkById(500, function ($chunk) use (&$rows): void {
                $rows = $rows->merge($chunk);
            });

        // Send Slack notification regardless of stock status (healthy vs low)
        $slackWebhook = config('services.low_stock.webhook_url');

        if (! blank($slackWebhook)) {
            Notification::route('slack', $slackWebhook)
                ->notify(new LowStockSlackReport($rows));
        } else {
            logger()->warning('SLACK_LOW_STOCK_WEBHOOK_URL is not configured.');
        }

        if ($rows->isEmpty()) {
            $this->info('No products have reached their minimum quantity.');
            return self::SUCCESS;
        }

        Mail::to($recipient)->queue(new LowStockReport($rows));

        $this->info("Low‑stock report sent to {$recipient}.");

        return self::SUCCESS;
    }
}
