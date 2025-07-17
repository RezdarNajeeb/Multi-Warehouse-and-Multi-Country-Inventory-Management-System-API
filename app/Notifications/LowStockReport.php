<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Notifications\Messages\SlackMessage;

class LowStockReport extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Collection $lowStocks, public array $channels = ['mail', 'slack'])
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
        return $this->channels;
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
            $product      = $lowStock->product;
            $supplier     = $product?->supplier;
            $contactInfo  = $supplier?->contact_info ?? [];

            // Core details
            $mail->line("• {$product->name} (SKU: {$product->sku})")
                 ->line("  Qty: {$lowStock->quantity} / Min: {$lowStock->min_quantity}")
                 ->line("  Warehouse: {$lowStock->warehouse->location} ({$lowStock->warehouse->country->name})");

            // Dynamic contact info handling
            if (!empty($contactInfo) && is_array($contactInfo)) {
                $mail->line("  Supplier Contact Info:");
                foreach ($contactInfo as $key => $value) {
                    $label = ucfirst(str_replace('_', ' ', $key));
                    $mail->line("    {$label}: {$value}");
                }
            } else {
                $mail->line("  Supplier Contact Info: N/A");
            }

            // Spacer line between products
            $mail->line('');
        }

        return $mail;
    }



    public function toSlack($notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->success()
            ->from('Inventory Bot')
            ->content('Daily Low Stock Report')
            ->attachment(function ($attachment) {
                foreach ($this->lowStocks as $item) {
                    $attachment->fields([
                        'Product'     => "{$item->product->name} ({$item->product->sku})",
                        'Qty / Min'   => "{$item->quantity} / {$item->min_quantity}",
                        'Warehouse'   => $item->warehouse->location,
                        'Country'     => $item->warehouse->country->name,
                    ]);
                }
            });
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
