<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->column = Column::make('name');
});

it('is key', function () {
    expect($this->column)
        ->isKey()->toBeFalse()
        ->key()->toBe($this->column)
        ->isKey()->toBeTrue();
});
