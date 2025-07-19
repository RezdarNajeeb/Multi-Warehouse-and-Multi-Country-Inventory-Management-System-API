<?php

use App\Events\LowStockDetected;
use App\Jobs\SendLowStockReport;
use App\Listeners\SendLowStockNotification;
use App\Models\{Country, Inventory, Product, Supplier, Warehouse};
use App\Notifications\LowStockReportNotification;
use App\Services\LowStockReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{Event, Notification, Queue};

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();
    Notification::fake();
    Event::fake();

    $this->country = Country::factory()->create();

    $this->warehouse = Warehouse::factory()->create([
        'country_id' => $this->country->id,
    ]);

    $this->supplier = Supplier::factory()->create([
        'contact_info' => ['email' => 'supplier@test.com', 'phone' => '+1234567890']
    ]);

    $this->product = Product::factory()->create([
        'supplier_id' => $this->supplier->id,
    ]);

    $this->lowStockInventory = Inventory::factory()->create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 5,
        'min_quantity' => 10,
    ]);

    Inventory::factory()->create([
        'product_id' => Product::factory()->create()->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 15,
        'min_quantity' => 10,
    ]);
});

it('dispatches job via artisan command', function () {
    $this->artisan('inventory:check-low-stock')
        ->expectsOutput('Low stock check has been dispatched.')
        ->assertExitCode(0);

    Queue::assertPushed(SendLowStockReport::class);
});

it('detects low stock using the service', function () {
    $result = app(LowStockReportService::class)();

    expect($result)->toHaveCount(1)
        ->and($result->first()->id)->toBe($this->lowStockInventory->id);
});

it('sends notification from job when low stock exists', function () {
    new SendLowStockReport()->handle();

    Notification::assertSentTo(
        Notification::route('mail', 'admin@test.com')->route('slack', 'https://hooks.slack.com/test'),
        LowStockReportNotification::class,
        fn ($notification) =>
            $notification->lowStocks->count() === 1 &&
            $notification->lowStocks->first()->is($this->lowStockInventory) &&
            $notification->channels === ['mail', 'slack']
    );
});

it('skips notification when no low stock exists', function () {
    Inventory::query()->update(['quantity' => 20]);

    new SendLowStockReport()->handle();

    Notification::assertNothingSent();
});

it('sends notification when event is fired', function () {
    $listener = new SendLowStockNotification();
    $listener->handle(new LowStockDetected(collect([$this->lowStockInventory])));

    Notification::assertSentTo(
        Notification::route('mail', 'admin@test.com'),
        LowStockReportNotification::class,
        fn ($notification) =>
            $notification->lowStocks->count() === 1 &&
            $notification->channels === ['mail']
    );
});

it('returns correct channels', function () {
    $notify = new LowStockReportNotification(collect([$this->lowStockInventory]), ['mail', 'slack']);
    expect($notify->via(new stdClass()))->toBe(['mail', 'slack']);

    $notify = new LowStockReportNotification(collect([$this->lowStockInventory]), ['mail']);
    expect($notify->via(new stdClass()))->toBe(['mail']);
});

it('handles multiple low stock items', function () {
    $secondProduct = Product::factory()->create(['supplier_id' => $this->supplier->id]);

    $low2 = Inventory::factory()->create([
        'product_id' => $secondProduct->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 3,
        'min_quantity' => 8,
    ]);

    $result = app(LowStockReportService::class)();

    expect($result)->toHaveCount(2)
        ->and($result->pluck('id'))->toContain($this->lowStockInventory->id, $low2->id);
});

it('executes full notification workflow', function () {
    $this->artisan('inventory:check-low-stock')->assertExitCode(0);
    Queue::assertPushed(SendLowStockReport::class);

    new SendLowStockReport()->handle();

    Notification::assertSentTo(
        Notification::route('mail', 'admin@test.com')->route('slack', 'https://hooks.slack.com/test'),
        LowStockReportNotification::class,
        fn ($notification) =>
            $notification->lowStocks->count() === 1 &&
            $notification->lowStocks->first()->product->is($this->product) &&
            $notification->lowStocks->first()->quantity === 5 &&
            $notification->channels === ['mail', 'slack']
    );
});
