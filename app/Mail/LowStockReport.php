<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;

class LowStockReport extends Mailable
{
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
