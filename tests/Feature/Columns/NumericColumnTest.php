<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;
use Honed\Table\Columns\NumericColumn;

beforeEach(function () {
    $this->column = NumericColumn::make('age');
});

it('is type numeric', function () {
    expect($this->column)
        ->getType()->toBe(Column::NUMERIC)
        ->getPlaceholder()->toBe('0');
});

it('does not format null values', function () {
    expect($this->column)
        ->format(null)->toBeNull();
});

it('formats numbers', function () {
    expect($this->column)
        ->format(123)->toBe('123');
});
