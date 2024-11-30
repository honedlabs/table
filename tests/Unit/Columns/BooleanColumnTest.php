<?php

use Honed\Table\Columns\BooleanColumn;
use Honed\Core\Formatters\BooleanFormatter;

beforeEach(function () {
    $this->column = BooleanColumn::make('name');
});

it('has type boolean', function () {
    expect($this->column->getType())->toBe('boolean');
});

it('has boolean formatter', function () {
    expect($this->column->hasFormatter())->toBeTrue();
    expect($this->column->getFormatter())->toBeInstanceOf(BooleanFormatter::class);
});