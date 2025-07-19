<?php

use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\InventoryTransferService;
use App\Events\LowStockDetected;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Event::fake();
    $this->service = app(InventoryTransferService::class);
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->supplier = Supplier::factory()->create();
    $this->product = Product::factory()->create(['supplier_id' => $this->supplier->id]);
    $this->sourceWarehouse = Warehouse::factory()->create(['name' => 'Source Warehouse']);
    $this->destinationWarehouse = Warehouse::factory()->create(['name' => 'Destination Warehouse']);

    $this->sourceInventory = Inventory::factory()->create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->sourceWarehouse->id,
        'quantity' => 100,
        'min_quantity' => 10,
    ]);

    $this->destinationInventory = Inventory::factory()->create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->destinationWarehouse->id,
        'quantity' => 50,
        'min_quantity' => 5,
    ]);
});

it('transfers inventory successfully between warehouses', function () {
    $transferData = [
        'product_id' => $this->product->id,
        'source_warehouse_id' => $this->sourceWarehouse->id,
        'destination_warehouse_id' => $this->destinationWarehouse->id,
        'quantity' => 30,
    ];

    [$data, $error, $status] = $this->service->transfer($transferData);

    expect($data)->toHaveKeys(['source_inventory', 'destination_inventory'])
        ->and($data['source_inventory'])->toBeInstanceOf(InventoryResource::class)
        ->and($data['destination_inventory'])->toBeInstanceOf(InventoryResource::class)
        ->and($error)->toBeNull()
        ->and($status)->toBe(Response::HTTP_CREATED);

    $this->sourceInventory->refresh();
    $this->destinationInventory->refresh();

    expect($this->sourceInventory->quantity)->toBe(70) // 100 - 30
    ->and($this->destinationInventory->quantity)->toBe(80); // 50 + 30
});

it('prevents transfer when insufficient stock in source warehouse', function () {
    $transferData = [
        'product_id' => $this->product->id,
        'source_warehouse_id' => $this->sourceWarehouse->id,
        'destination_warehouse_id' => $this->destinationWarehouse->id,
        'quantity' => 150, // More than available (100)
    ];

    [$data, $error, $status] = $this->service->transfer($transferData);

    expect($data)->toBeNull()
        ->and($error)->toBe('Insufficient stock in the source warehouse')
        ->and($status)->toBe(Response::HTTP_UNPROCESSABLE_ENTITY);

    $this->sourceInventory->refresh();
    $this->destinationInventory->refresh();

    expect($this->sourceInventory->quantity)->toBe(100) // Unchanged
    ->and($this->destinationInventory->quantity)->toBe(50); // Unchanged
});

it('dispatches low stock event when source inventory falls below minimum', function () {
    $transferData = [
        'product_id' => $this->product->id,
        'source_warehouse_id' => $this->sourceWarehouse->id,
        'destination_warehouse_id' => $this->destinationWarehouse->id,
        'quantity' => 95, // Will leave 5, which is below min_quantity of 10
    ];

    $this->service->transfer($transferData);

    expect(Event::hasDispatched(LowStockDetected::class))->toBeTrue();
});

it('records both out and in transactions correctly', function () {
    $transferData = [
        'product_id' => $this->product->id,
        'source_warehouse_id' => $this->sourceWarehouse->id,
        'destination_warehouse_id' => $this->destinationWarehouse->id,
        'quantity' => 25,
    ];

    $this->service->transfer($transferData);

    $transactions = InventoryTransaction::where('product_id', $this->product->id)
        ->orderBy('created_at')
        ->get();

    expect($transactions)->toHaveCount(2);

    $outTransaction = $transactions->first();
    $inTransaction = $transactions->last();

    expect($outTransaction->warehouse_id)->toBe($this->sourceWarehouse->id)
        ->and($outTransaction->quantity)->toBe(25)
        ->and($outTransaction->created_by)->toBe($this->user->id)
        ->and($inTransaction->warehouse_id)->toBe($this->destinationWarehouse->id)
        ->and($inTransaction->quantity)->toBe(25)
        ->and($inTransaction->created_by)->toBe($this->user->id);
});
