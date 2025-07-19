<?php

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\LowStockReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->lowStockReportService = app(LowStockReportService::class);
    $this->supplier = Supplier::factory()->create();
    $this->warehouse1 = Warehouse::factory()->create(['name' => 'Main Warehouse']);
    $this->warehouse2 = Warehouse::factory()->create(['name' => 'Secondary Warehouse']);
});

it('identifies products reached their minimum required quantity', function () {
    $product1 = Product::factory()->create(['supplier_id' => $this->supplier->id]);
    $product2 = Product::factory()->create(['supplier_id' => $this->supplier->id]);

    // Low stock inventory
    Inventory::factory()->create([
        'product_id' => $product1->id,
        'warehouse_id' => $this->warehouse1->id,
        'quantity' => 5,
        'min_quantity' => 10,
    ]);

    // Normal stock inventory
    Inventory::factory()->create([
        'product_id' => $product2->id,
        'warehouse_id' => $this->warehouse1->id,
        'quantity' => 20,
        'min_quantity' => 10,
    ]);

    $lowStockProducts = ($this->lowStockReportService)();

    expect($lowStockProducts)->toHaveCount(1)
        ->and($lowStockProducts->first()->product_id)->toBe($product1->id);
});

it('returns the low stock report with correct fields', function () {
    $product = Product::factory()->create(['supplier_id' => $this->supplier->id]);

    Inventory::factory()->create([
        'product_id' => $product->id,
        'warehouse_id' => $this->warehouse1->id,
        'quantity' => 5,
        'min_quantity' => 10,
    ]);

    $report = ($this->lowStockReportService)();

    expect($report)->toBeInstanceOf(Collection::class)
        ->and($report)->not->toBeEmpty()
        ->and($report->first())->toHaveKeys([
            'id',
            'product_id',
            'warehouse_id',
            'quantity',
            'min_quantity',
            'product',
            'warehouse'
        ])
        ->and($report->first()['product'])->toHaveKeys(['id', 'name', 'sku', 'supplier'])
        ->and($report->first()['warehouse'])->toHaveKeys(['id', 'location', 'country']);
});
