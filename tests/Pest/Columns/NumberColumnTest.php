<?php

use Honed\Core\Formatters\NumberFormatter;
use Honed\Table\Columns\NumberColumn;

beforeEach(function () {
    $this->test = NumberColumn::make('name');
});

it('is type number', function () {
    expect($this->test->getType())->toBe('col:number');
});

it('has number formatter', function () {
    expect($this->test)
        ->hasFormatter()->toBeTrue()
        ->getFormatter()->toBeInstanceOf(NumberFormatter::class);
});
