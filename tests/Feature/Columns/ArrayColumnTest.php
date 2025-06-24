<?php

declare(strict_types=1);

use Honed\Table\Columns\ArrayColumn;
use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->column = ArrayColumn::make('categories');
});

it('is type array', function () {
    expect($this->column)
        ->getType()->toBe(Column::ARRAY);
});

it('does not format null values', function () {
    expect($this->column)
        ->format(null)->toBeNull();
});

it('formats array', function () {
    expect($this->column)
        ->glue(', ')->toBe($this->column)
        ->format([1, 2, 3])->toBe('1, 2, 3');
});
