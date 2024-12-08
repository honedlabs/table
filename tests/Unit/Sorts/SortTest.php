<?php

use Honed\Table\Sorts\Sort;

it('can be created', function () {
    expect(Sort::make('name'))->toBeInstanceOf(Sort::class);
});
