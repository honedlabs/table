<?php

declare(strict_types=1);

use Honed\Table\Facades\Views;
use Honed\Table\Table;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->product = Product::factory()->create();

    $this->interaction = Views::for($this->product);
});

it('has scope', function () {
    $product = Product::factory()->create();

    $this->interaction->for($product);

    expect($this->interaction->getScope())
        ->toBeArray()
        ->toHaveCount(2)
        ->each(fn ($scope) => $scope->toBeInstanceOf(Product::class));
});

it('loads', function () {
    expect($this->interaction->load(Table::make()))
        ->toBeArray()
        ->toBeEmpty();
});
