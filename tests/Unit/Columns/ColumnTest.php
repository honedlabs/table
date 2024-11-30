<?php

use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->column = Column::make('name');
});

it('has type default', function () {
    expect($this->column->getType())->toBe('default');
});

it('has no formatter', function () {
    expect($this->column->hasFormatter())->toBeFalse();
});