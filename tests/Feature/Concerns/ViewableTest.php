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
        ->isNotViewable()->toBeTrue()
        ->isViewable()->toBeFalse()
        ->getViews()->toBeNull()
        ->viewable()->toBe($this->table)
        ->isViewable()->toBeTrue()
        ->getViews()->toBeInstanceOf(PendingViewInteraction::class)
        ->notViewable()->toBe($this->table)
        ->isNotViewable()->toBeTrue()
        ->getViews()->toBeNull();
});

it('is viewable via contract', function () {
    expect(ProductTable::make())
        ->isViewable()->toBeTrue();
});

it('is viewable with scopes', function () {
    expect($this->table)
        ->getViews()->toBeNull()
        ->viewable(Product::factory()->create())->toBe($this->table)
        ->getViews()->toBeInstanceOf(PendingViewInteraction::class);
});

it('loads views', function () {
    expect($this->table)
        ->listViews()->toBeNull()
        ->viewable(Product::factory()->create())->toBe($this->table)
        ->listViews()->toBeArray();
});
