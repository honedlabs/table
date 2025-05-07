<?php

declare(strict_types=1);

use Honed\Table\Attributes\Pagination;
use Honed\Table\Tests\Stubs\Product;

it('has attribute', function () {
    $pagination = ['id', 'name'];

    $attribute = new Pagination($pagination);

    expect($attribute)
        ->toBeInstanceOf(Pagination::class)
        ->pagination->toBe($pagination);

    // expect(Product::class)
    //     ->toHaveAttribute(Select::class);
});

