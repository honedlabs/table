<?php

declare(strict_types=1);

use Honed\Table\PendingViewInteraction;
use Honed\Table\Table;
use Workbench\App\Models\Product;
use Workbench\App\Tables\ProductTable;

beforeEach(function () {
    $this->table = Table::make();
});

it('is viewable', function () {
    expect($this->table)
        ->isViewable()->toBeFalse()
        ->getViews()->toBeNull()
        ->viewable()->toBe($this->table)
        ->isViewable()->toBeTrue()
        ->getViews()->toBeInstanceOf(PendingViewInteraction::class);
});

it('is orderable via contract', function () {
    expect(ProductTable::make())
        ->isOrderable()->toBeTrue();
});

it('is viewable with scopes', function () {
    expect($this->table)
        ->getViews()->toBeNull()
        ->viewable(Product::factory()->create())->toBe($this->table)
        ->getViews()->toBeInstanceOf(PendingViewInteraction::class);
});

it('loads views', function () {
    expect($this->table)
        ->loadViews()->toBeNull()
        ->viewable(Product::factory()->create())->toBe($this->table)
        ->loadViews()->toBeArray();
});
