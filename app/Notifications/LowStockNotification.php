<?php

namespace App\Notifications;

use App\Models\Inventory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
  use Queueable;

  public function __construct(protected Inventory $inventory)
  {
    //
  }

  /**
   * Get the notification's delivery channels.
   */
  public function via(mixed $notifiable): array
  {
    return ['mail'];
  }

  /**
   * Get the mail representation of the notification.
   */
  public function toMail(mixed $notifiable): MailMessage
  {
    $product = $this->inventory->product;
    $warehouse = $this->inventory->warehouse;
    $country = $warehouse->country;

    return (new MailMessage)
      ->subject("Low Stock Alert: {$product->name}")
      ->line("Product: {$product->name} (SKU: {$product->sku})")
      ->line("Current Quantity: {$this->inventory->quantity}")
      ->line("Minimum Required Quantity: {$this->inventory->min_quantity}")
      ->line("Warehouse: {$warehouse->name} ({$warehouse->location})")
      ->line("Country: {$country->name}")
      ->line('Please restock as soon as possible.');
  }
}
