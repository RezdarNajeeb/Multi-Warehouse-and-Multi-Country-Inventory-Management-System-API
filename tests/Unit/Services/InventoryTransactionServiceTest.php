<?php

use App\Http\Resources\InventoryTransactionResource;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\InventoryTransactionService;
use App\Events\LowStockDetected;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Event::fake();
    $this->service = app(InventoryTransactionService::class);
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->supplier = Supplier::factory()->create();
    $this->product = Product::factory()->create(['supplier_id' => $this->supplier->id]);
    $this->warehouse = Warehouse::factory()->create();

    $this->inventory = Inventory::factory()->create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 100,
        'min_quantity' => 10,
    ]);
});

it('paginates inventory transactions successfully', function () {
    InventoryTransaction::factory()->count(15)->create([
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'created_by' => $this->user->id,
    ]);

    $result = $this->service->list();

    expect($result)->toBeInstanceOf(CursorPaginator::class)
        ->and($result->count())->toBe(10);
});

it('records stock in transaction successfully', function () {
    $transactionData = [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 50,
        'transaction_type' => 'in',
    ];

    [$resource, $error, $status] = $this->service->record($transactionData);

    expect($resource)->toBeInstanceOf(InventoryTransactionResource::class)
        ->and($error)->toBeNull()
        ->and($status)->toBe(Response::HTTP_CREATED);

    $this->inventory->refresh();
    expect($this->inventory->quantity)->toBe(150); // 100 + 50
});

it('records stock out transaction successfully', function () {
    $transactionData = [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 30,
        'transaction_type' => 'out',
    ];

    [$resource, $error, $status] = $this->service->record($transactionData);

    expect($resource)->toBeInstanceOf(InventoryTransactionResource::class)
        ->and($error)->toBeNull()
        ->and($status)->toBe(Response::HTTP_CREATED);

    $this->inventory->refresh();
    expect($this->inventory->quantity)->toBe(70); // 100 - 30
});

it('prevents stock out when insufficient stock available', function () {
    $transactionData = [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 150, // More than available (100)
        'transaction_type' => 'out',
        'notes' => 'Attempted oversell',
    ];

    [$resource, $error, $status] = $this->service->record($transactionData);

    expect($resource)->toBeNull()
        ->and($error)->toBe('Insufficient stock')
        ->and($status)->toBe(Response::HTTP_UNPROCESSABLE_ENTITY);

    $this->inventory->refresh();
    expect($this->inventory->quantity)->toBe(100); // Unchanged
});

it('dispatches low stock event when quantity falls below minimum', function () {
    $transactionData = [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 95, // Will leave 5, which is below min_quantity of 10
        'transaction_type' => 'out',
        'notes' => 'Large sale',
    ];

    $this->service->record($transactionData);

    expect(Event::hasDispatched(LowStockDetected::class))->toBeTrue();
});

it('handles database transaction rollback on failure', function () {
    // Create a scenario that would cause a database constraint violation
    $transactionData = [
        'product_id' => 99999, // Non-existent product
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 10,
        'transaction_type' => 'in',
    ];

    expect(fn() => $this->service->record($transactionData))
        ->toThrow(ModelNotFoundException::class)
        ->and(InventoryTransaction::count())->toBe(0); // Ensure no transaction was recorded
});

it('handles exact stock depletion correctly', function () {
    $transactionData = [
        'product_id' => $this->product->id,
        'warehouse_id' => $this->warehouse->id,
        'quantity' => 100, // Exact available quantity
        'transaction_type' => 'out',
    ];

    [$resource, $error, $status] = $this->service->record($transactionData);

    expect($resource)->toBeInstanceOf(InventoryTransactionResource::class)
        ->and($error)->toBeNull()
        ->and($status)->toBe(Response::HTTP_CREATED);

    $this->inventory->refresh();

    expect($this->inventory->quantity)->toBe(0)
        ->and(Event::hasDispatched(LowStockDetected::class))->toBeTrue();
});
