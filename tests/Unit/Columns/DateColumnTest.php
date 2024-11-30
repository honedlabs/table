<?php

use Honed\Table\Columns\DateColumn;
use Honed\Core\Formatters\DateFormatter;

beforeEach(function () {
    $this->column = DateColumn::make('name');
});

it('has type date', function () {
    expect($this->column->getType())->toBe('date');
});

it('has date formatter', function () {
    expect($this->column->hasFormatter())->toBeTrue();
    expect($this->column->getFormatter())->toBeInstanceOf(DateFormatter::class);
});