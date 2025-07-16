<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class LowStockReport extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Collection $lowStocks)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Daily Low Stock Report')
            ->greeting('Hello,')
            ->line('The following products are below their minimum stock levels:');

        foreach ($this->lowStocks as $lowStock) {
            $mail->line("• {$lowStock['product_name']} (SKU: {$lowStock['sku']})")
                ->line("  Qty: {$lowStock['quantity']} / Min: {$lowStock['min_quantity']}")
                ->line("  Warehouse: {$lowStock['warehouse_location']} ({$lowStock['country']})")

                ->line('');
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
