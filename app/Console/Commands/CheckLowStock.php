<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendLowStockReport;
class CheckLowStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:check-low-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a low stock report and send it to the configured email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SendLowStockReport::dispatch();
    }
}
