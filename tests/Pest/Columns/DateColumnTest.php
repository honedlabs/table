<?php

use Honed\Core\Formatters\DateFormatter;
use Honed\Table\Columns\DateColumn;

beforeEach(function () {
    $this->test = DateColumn::make('name');
});

it('is type date', function () {
    expect($this->test->getType())->toBe('col:date');
});

it('has date formatter', function () {
    expect($this->test)
        ->hasFormatter()->toBeTrue()
        ->getFormatter()->toBeInstanceOf(DateFormatter::class);
});
