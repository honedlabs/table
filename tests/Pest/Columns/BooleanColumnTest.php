<?php

use Honed\Core\Formatters\BooleanFormatter;
use Honed\Table\Columns\BooleanColumn;

beforeEach(function () {
    $this->column = BooleanColumn::make('name');
});

it('has type boolean', function () {
    expect($this->column->getType())->toBe('col:bool');
});

it('has boolean formatter', function () {
    expect($this->column->hasFormatter())->toBeTrue();
    expect($this->column->getFormatter())->toBeInstanceOf(BooleanFormatter::class);
});
