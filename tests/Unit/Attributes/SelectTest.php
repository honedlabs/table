<?php

declare(strict_types=1);

use Honed\Table\Attributes\Select;
use Honed\Table\Tests\Stubs\Product;

it('has attribute', function () {
    $selects = ['id', 'name'];

    $attribute = new Select($selects);

    expect($attribute)
        ->toBeInstanceOf(Select::class)
        ->select->toBe($selects);

    // expect(Product::class)
    //     ->toHaveAttribute(Select::class);
});

