<?php

use Honed\Core\Formatters\StringFormatter;
use Honed\Table\Columns\TextColumn;

beforeEach(function () {
    $this->column = TextColumn::make('name');
});

it('has type text', function () {
    expect($this->column->getType())->toBe('text');
});

it('has string formatter', function () {
    expect($this->column->hasFormatter())->toBeTrue();
    expect($this->column->getFormatter())->toBeInstanceOf(StringFormatter::class);
});
