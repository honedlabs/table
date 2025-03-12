<?php

declare(strict_types=1);

use Honed\Action\ActionFactory;
use Honed\Table\Table;
use Honed\Table\Tests\Fixtures\Table as FixtureTable;
use Honed\Table\Tests\Stubs\Product;
use Illuminate\Support\Arr;

use function Pest\Laravel\post;

beforeEach(function () {
    $this->table = FixtureTable::make();
});

it('has a route key', function () {
    $key = $this->table->getRouteKey();

    expect($this->table)
        ->decode($key)->toBe(FixtureTable::class);
});

it('can resolve a route binding', function () {
    expect(Table::make())
        ->resolveRouteBinding($this->table->getRouteKey())->toBeInstanceOf(FixtureTable::class);
});

it('can handle inline actions at endpoint', function () {
    $product = product();

    $data = [
        'table' => $this->table->getRouteKey(),
        'type' => ActionFactory::Inline,
        'name' => 'edit',
        'id' => $product->id,
    ];

    post(route('table.actions'), $data)
        ->assertRedirect('/');

    expect($product->refresh())
        ->name->toBe('Inline');
});

it('can authorize the action', function () {
    $a = product();
    $b = product();

    $data = [
        'table' => $this->table->getRouteKey(),
        'type' => ActionFactory::Inline,
        'name' => 'delete',
        'id' => $a->id,
    ];

    post(route('table.actions'), $data)
        ->assertStatus(403);

    Arr::set($data, 'id', $b->id);

    post(route('table.actions'), $data)
        ->assertRedirect('/');

    $this->assertDatabaseHas('products', [
        'id' => $a->id,
    ]);

    $this->assertDatabaseMissing('products', [
        'id' => $b->id,
    ]);
});

it('can handle bulk actions at endpoint', function () {
    populate(100);

    $ids = range(1, 50);

    $data = [
        'table' => $this->table->getRouteKey(),
        'type' => ActionFactory::Bulk,
        'name' => 'edit',
        'all' => false,
        'except' => [],
        'only' => $ids,
    ];

    post(route('table.actions'), $data)
        ->assertRedirect('/');

    $products = Product::whereIn('id', $ids)->get();

    expect($products)
        ->each(fn ($product) => $product->name->toBe('Bulk')
        );
});

it('can handle specific tables', function () {
    $data = [
        'type' => ActionFactory::Page,
        'name' => 'factory',
    ];

    post(route('products.table', $this->table->getRouteKey()), $data)
        ->assertRedirect('/products/1');

    expect(Product::all())
        ->toHaveCount(1);

    post(route('products.table', Table::encode(Table::class)), $data)
        ->assertStatus(404);
});
