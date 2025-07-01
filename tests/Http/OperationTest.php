<?php

declare(strict_types=1);

use Workbench\App\Models\Product;
use Workbench\App\Tables\ProductTable;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->table = ProductTable::make();
});

it('handles inline operation', function () {
    $product = Product::factory()->create();

    post(route('actions', [$this->table, 'edit'], [
        'id' => $product->id,
    ]))->assertRedirect();

    assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => $product->name,
    ]);
});

it('handles page operation', function () {
    post(route('actions', [$this->table, 'factory']))
        ->assertRedirect();

    assertDatabaseCount('products', 1);
});
