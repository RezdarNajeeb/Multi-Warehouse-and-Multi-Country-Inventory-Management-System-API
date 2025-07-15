<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class LowStockReport extends Mailable implements ShouldQueue
{
    use Queueable;
    public function __construct(public Collection $rows)
    {
        //
    }

    public function build(): self
    {
        return $this->subject('Low Stock Report')
            ->view('emails.low_stock_report');
    }
}
