<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Notifications\Messages\SlackMessage;

class LowStockReportNotification extends Notification implements ShouldQueue
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
            ->subject('📉 Daily Low Stock Report')
            ->greeting('Hello,')
            ->line('Here is the list of products that are currently reached their minimum stock levels:')
            ->line('');

        foreach ($this->lowStocks as $lowStock) {
            $product = $lowStock->product;
            $supplier = $product?->supplier;
            $contactInfo = $supplier?->contact_info ?? [];

            $mail->line("🔹 **Product**: {$product->name} (`{$product->sku}`)")
                ->line("• **Current Quantity**: {$lowStock->quantity} / **Min Required**: {$lowStock->min_quantity}")
                ->line("• **Warehouse Location**: {$lowStock->warehouse->location}")
                ->line("• **Country**: {$lowStock->warehouse->country->name}");

            if (!empty($contactInfo) && is_array($contactInfo)) {
                $mail->line("• **Supplier Contact Info:**");

                foreach ($contactInfo as $key => $value) {
                    $label = ucfirst(str_replace('_', ' ', $key));
                    $mail->line("  - {$label}: {$value}");
                }
            } else {
                $mail->line("• **Supplier Contact Info**: N/A");
            }

            $mail->line('---');
        }

        return $mail->line('This report is automatically generated at 12 AM daily.');
    }


    public function toSlack($notifiable): SlackMessage
    {
        $message = (new SlackMessage)
            ->from('Inventory Bot')
            ->success()
            ->content('*📉 Daily Low Stock Report*')
            ->content('The following products are below their minimum stock level:');

        foreach ($this->lowStocks as $item) {
            $fields = [
                '📦 Product'     => "*{$item->product->name}* (`{$item->product->sku}`)",
                '📊 Current Qty / Min Required'   => "{$item->quantity} / {$item->min_quantity}",
                '🏬 Warehouse Location'   => $item->warehouse->location,
                '🌍 Country'     => $item->warehouse->country->name,
            ];

            // Add supplier contact info as grouped fields
            $supplierInfo = $item->product?->supplier?->contact_info ?? [];

            if (!empty($supplierInfo)) {
                $fields['👤 Supplier Contact Info'] = '──────────────';
                $fields[''] = ''; // Add an empty field for layout

                foreach ($supplierInfo as $key => $value) {
                    $label = ucfirst(str_replace('_', ' ', $key));
                    $emoji = match ($key) {
                        'email' => '📧',
                        'phone', 'mobile' => '📞',
                        'website' => '🔗',
                        default => '🔸',
                    };
                    $fields["{$emoji} {$label}"] = $value;
                }
            } else {
                $fields['👤 Supplier Contact Info'] = 'N/A';
            }

            $message->attachment(function ($attachment) use ($fields) {
                $attachment->fields($fields);
            });
        }

        return $message;
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
