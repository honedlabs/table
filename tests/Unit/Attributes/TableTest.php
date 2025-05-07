<?php

declare(strict_types=1);

use Honed\Table\Attributes\Table;
use Honed\Table\Tests\Fixtures\Table as FixturesTable;
use Honed\Table\Tests\Stubs\Product;

it('has attribute', function () {
    $attribute = new Table(FixturesTable::class);

    expect($attribute)
        ->toBeInstanceOf(Table::class)
        ->table->toBe(FixturesTable::class);

    expect(Product::class)
        ->toHaveAttribute(Table::class);
});

