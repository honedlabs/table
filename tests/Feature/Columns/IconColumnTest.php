<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;
use Honed\Table\Columns\IconColumn;

beforeEach(function () {
    $this->column = IconColumn::make('icon');
});

it('is type icon', function () {
    expect($this->column)
        ->getType()->toBe(Column::ICON);
});
