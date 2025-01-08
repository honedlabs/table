<?php

use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->test = Column::make('name');
});

it('is type default', function () {
    expect($this->test->getType())->toBe('col:default');
});

it('has no formatter', function () {
    expect($this->test->hasFormatter())->toBeFalse();
});
