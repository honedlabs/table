<?php

declare(strict_types=1);

use Honed\Table\Columns\BooleanColumn;
use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->column = BooleanColumn::make('is_active')
        ->trueText('Yes')
        ->falseText('No');
});

it('is type array', function () {
    expect($this->column)
        ->getType()->toBe(Column::BOOLEAN);
});

it('formats boolean values', function ($value, $expected) {
    expect($this->column)
        ->format($value)->toBe($expected);
})->with([
    [false, 'No'],
    [null, 'No'],
    [0, 'No'],
    [1, 'Yes'],
    ['string', 'Yes'],
    [true, 'Yes'],
]);
