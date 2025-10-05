<?php

declare(strict_types=1);

use Honed\Table\Columns\BadgeColumn;

beforeEach(function () {
    $this->column = BadgeColumn::make('status');
});

it('is type badge', function () {
    expect($this->column)
        ->getType()->toBe('badge')
        ->isBadge()->toBeTrue();
});
