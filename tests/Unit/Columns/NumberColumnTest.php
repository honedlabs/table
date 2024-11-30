<?php

use Honed\Table\Columns\NumberColumn;
use Honed\Core\Formatters\NumberFormatter;

beforeEach(function () {
    $this->column = NumberColumn::make('name');
});

it('has type number', function () {
    expect($this->column->getType())->toBe('number');
});

it('has number formatter', function () {
    expect($this->column->hasFormatter())->toBeTrue();
    expect($this->column->getFormatter())->toBeInstanceOf(NumberFormatter::class);
});