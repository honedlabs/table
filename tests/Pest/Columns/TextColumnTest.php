<?php

use Honed\Core\Formatters\StringFormatter;
use Honed\Table\Columns\TextColumn;

beforeEach(function () {
    $this->test = TextColumn::make('name');
});

it('has type text', function () {
    expect($this->test->getType())->toBe('col:text');
});

it('has string formatter', function () {
    expect($this->test)
        ->hasFormatter()->toBeTrue()
        ->getFormatter()->toBeInstanceOf(StringFormatter::class);
});
