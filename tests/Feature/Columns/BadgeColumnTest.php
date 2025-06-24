<?php

declare(strict_types=1);

use Honed\Table\Columns\BadgeColumn;
use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->column = BadgeColumn::make('status');
});

it('is type badge', function () {
    expect($this->column)
        ->getType()->toBe(Column::BADGE)
        ->isBadge()->toBeTrue();
});
