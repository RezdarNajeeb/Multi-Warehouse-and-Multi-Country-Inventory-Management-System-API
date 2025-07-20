<?php

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\InventoryService;
use App\Events\LowStockDetected;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Event::fake(); // Prevent actual event dispatching during tests
    $this->service = app(InventoryService::class);
    $this->supplier = Supplier::factory()->create();
    $this->product = Product::factory()->create(['supplier_id' => $this->supplier->id]);
    $this->warehouse = Warehouse::factory()->create(['name' => 'Warehouse A']);
});

it('creates an inventory successfully', function () {
    $data = [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 100,
        'min_quantity' => 10,
    ];

    $inventory = $this->service->create($data);

    expect($inventory)->toBeInstanceOf(Inventory::class)
        ->product_id->toBe($this->product->id)
        ->warehouse_id->toBe($this->warehouse->id)
        ->quantity->toBe(100)
        ->min_quantity->toBe(10);
});

it('dispatches low stock event when creating inventory with low stock', function () {
    $data = [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 5,
        'min_quantity' => 10,
    ];

    $this->service->create($data);

    expect(Event::hasDispatched(LowStockDetected::class))->toBeTrue();
});

it('updates all inventory fields successfully when no stock or history exists', function () {
    $inventory = Inventory::factory()->create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 0,
        'min_quantity' => 10,
    ]);

    $updateData = [
        'product_id' => Product::factory()->create()->id,
        'warehouse_id' => Warehouse::factory()->create()->id,
        'quantity' => 50,
        'min_quantity' => 5,
    ];

    [$updated, $message] = $this->service->update($updateData, $inventory);

    expect($updated)
        ->product_id->toBe($updateData['product_id']) // Updated product_id
        ->warehouse_id->toBe($updateData['warehouse_id']) // Updated warehouse_id
        ->quantity->toBe(50)
        ->min_quantity->toBe(5)
        ->and($message)->toBe('Inventory updated successfully');
});

it('only updates quantity and min_quantity when stock exists', function () {
    $inventory = Inventory::factory()->create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 100,
        'min_quantity' => 10,
    ]);

    $updateData = [
        'product_id' => Product::factory()->create()->id,
        'warehouse_id' => Warehouse::factory()->create()->id,
        'quantity' => 50,
        'min_quantity' => 5,
    ];

    [$updated, $message] = $this->service->update($updateData, $inventory);

    expect($updated)
        ->product_id->toBe($this->product->id) // Original product_id remains
        ->warehouse_id->toBe($this->warehouse->id) // Original warehouse_id remains
        ->quantity->toBe(50)
        ->min_quantity->toBe(5)
        ->and($message)->toBe('Only quantity and min_quantity were updated because stock or history exists.');
});

it('only updates quantity and min_quantity when history exists', function () {
    $inventory = Inventory::factory()->create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 1,
        'min_quantity' => 10,
    ]);

    // creating a history
    $inventory->transactions()->create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 1, // makes the stock to be zero
        'transaction_type' => 'out',
        'date' => now(),
        'created_by' => User::factory()->create()->id,
    ]);

    $updateData = [
        'product_id' => Product::factory()->create()->id,
        'warehouse_id' => Warehouse::factory()->create()->id,
        'quantity' => 50,
        'min_quantity' => 5,
    ];

    [$updated, $message] = $this->service->update($updateData, $inventory);

    expect($updated)
        ->product_id->toBe($this->product->id) // Original product_id remains
        ->warehouse_id->toBe($this->warehouse->id) // Original warehouse_id remains
        ->quantity->toBe(50)
        ->min_quantity->toBe(5)
        ->and($message)->toBe('Only quantity and min_quantity were updated because stock or history exists.');
});

it('deletes inventory successfully when no stock or history exists', function () {
    $inventory = Inventory::factory()->create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 0,
        'min_quantity' => 10,
    ]);

    $result = $this->service->delete($inventory);

    expect($result)->toBeNull()
        ->and(Inventory::find($inventory->id))->toBeNull();
});

it('prevents deletion when stock exists', function () {
    $inventory = Inventory::factory()->create([
        'product_id' => $this->product->id,
        'quantity' => 100
    ]);

    $result = $this->service->delete($inventory);

    expect($result)->toBe('Inventory cannot be deleted (stock or history exists).')
        ->and(Inventory::find($inventory->id))->not->toBeNull();
});

it('prevents deletion when history exists', function () {
    $inventory = Inventory::factory()->create([
        'product_id' => $this->product->id,
        'quantity' => 1
    ]);

    // creating a history
    $inventory->transactions()->create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 1, // makes the stock to be zero
        'transaction_type' => 'out',
        'date' => now(),
        'created_by' => User::factory()->create()->id,
    ]);

    $result = $this->service->delete($inventory);

    expect($result)->toBe('Inventory cannot be deleted (stock or history exists).')
        ->and(Inventory::find($inventory->id))->not->toBeNull();
});

it('paginates inventory list successfully', function () {
    Inventory::factory()->count(15)->create(['product_id' => $this->product->id]);

    $result = $this->service->list(10);

    expect($result)->toBeInstanceOf(CursorPaginator::class)
        ->and($result->count())->toBe(10);
});

it('returns global view with filters', function () {
    Inventory::factory()->count(5)->create(['product_id' => $this->product->id]);

    $result = $this->service->getGlobalView();

    expect($result)->toBeCollection()
        ->and($result->count())->toBeGreaterThan(0);
});
