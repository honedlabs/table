<?php

declare(strict_types=1);

use Honed\Table\Columns\HiddenColumn;

beforeEach(function () {
    $this->column = HiddenColumn::make('id');
});

it('is type hidden', function () {
    expect($this->column)
        ->getType()->toBeNull()
        ->isHidden()->toBeTrue();
});
