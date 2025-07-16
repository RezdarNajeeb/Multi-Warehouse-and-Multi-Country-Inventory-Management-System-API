<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class LowStockSlackReport extends Notification implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new notification instance.
   */
  public function __construct(private Collection $rows)
  {
    //
  }

  /**
   * Get the notification's delivery channels.
   */
  public function via(mixed $notifiable): array
  {
    return ['slack'];
  }

  /**
   * Build the Slack representation of the notification.
   */
  public function toSlack(mixed $notifiable): SlackMessage
  {
    // Fallback when everything is fine.
    if ($this->rows->isEmpty()) {
      return (new SlackMessage)
        ->to(config('services.low_stock.channel'))
        ->success()
        ->content('All stock is healthy 🎉');
    }

    $message = (new SlackMessage)
      ->warning()
      ->to(config('services.low_stock.channel'))
      ->content('*Daily Low Stock Report*');

    foreach ($this->rows as $inventory) {
      $product   = $inventory->product;
      $warehouse = $inventory->warehouse;

      $message->attachment(function ($attachment) use ($inventory, $product, $warehouse): void {
        $attachment->title($product->name)
          ->fields([
            'SKU'       => $product->sku,
            'Warehouse' => $warehouse->name,
            'Quantity'  => (string) $inventory->quantity,
            'Min Qty'   => (string) $inventory->min_quantity,
          ]);
      });
    }

    return $message;
  }
}
