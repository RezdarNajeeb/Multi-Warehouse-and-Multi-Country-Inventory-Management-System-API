<?php

use App\Models\Product;
use App\Models\Supplier;
use App\Services\ProductService;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Cache::store('array')->flush();
    $this->service = app(ProductService::class);
    $this->supplier = Supplier::factory()->create();
});

it('creates a product successfully', function () {
    $data = [
        'name' => 'Test Product',
        'sku' => 'TP001',
        'status' => false,
        'price' => 10,
        'supplier_id' => $this->supplier->id,
    ];

    $product = $this->service->create($data);

    expect($product)->toBeInstanceOf(Product::class)
        ->name->toBe('Test Product')
        ->sku->toBe('TP001');
});

it('finds a product from the database and caches it', function () {
    $product = Product::factory()->create(['supplier_id' => $this->supplier->id]);

    $result = $this->service->find($product->id);

    expect($result)->id->toBe($product->id);

    $cached = Cache::store('array')->get("products:single:{$product->id}");

    expect($cached)->id->toBe($product->id);
});

it('returns product from cache if available', function () {
    $product = Product::factory()->create(['supplier_id' => $this->supplier->id]);

    $result = $this->service->find($product->id);

    expect($result)->name->toBe($product->name);
});

it('updates a product successfully', function () {
    $product = Product::factory()->create(['name' => 'Old Name', 'supplier_id' => $this->supplier->id]);

    $updated = $this->service->update(['name' => 'New Name'], $product);

    expect($updated)->name->toBe('New Name');
});

it('deletes a product successfully', function () {
    $product = Product::factory()->create(['supplier_id' => $this->supplier->id]);

    $this->service->delete($product);

    expect(Product::find($product->id))->toBeNull();
});

it('paginates products and caches result', function () {
    Product::factory()->count(15)->create(['supplier_id' => $this->supplier->id]);
    $cursor = 'first';
    $perPage = 10;
    $cacheKey = "products:paginate:{$perPage}:{$cursor}";

    $result = $this->service->list($perPage);

    expect($result)->toBeInstanceOf(CursorPaginator::class);

    $cached = Cache::store('array')->get($cacheKey);
    expect($cached)->not->toBeNull();
});
