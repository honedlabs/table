<?php

declare(strict_types=1);

use Honed\Table\Concerns\HasTableBindings;
use Honed\Table\Table;
use Honed\Table\Tests\Fixtures\Table as FixturesTable;
use Honed\Table\Tests\Stubs\Product;

beforeEach(function () {
    $this->test = new class {
        use HasTableBindings;
    };
});

it('has route key', function () {
    expect($this->test)
        ->getRouteKey()->toBeString();
});

it('gets route key name', function () {
    expect($this->test)
        ->getRouteKeyName()->toBe('table');
});

it('resolves route binding', function () {
    expect($this->test)
        ->resolveRouteBinding(Table::encode(FixturesTable::class))
        ->toBeInstanceOf(FixturesTable::class);

    expect($this->test)
        ->resolveRouteBinding(Table::encode(Product::class))
        ->toBeNull();
});

it('has child binding the same as route binding', function () {
    $encoded = Table::encode(FixturesTable::class);

    expect($this->test)
        ->resolveChildRouteBinding(null, $encoded, null)
        ->toBeInstanceOf(FixturesTable::class);
});






