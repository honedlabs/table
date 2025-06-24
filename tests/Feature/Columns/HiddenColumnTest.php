<?php

declare(strict_types=1);

use Honed\Table\Columns\HiddenColumn;

beforeEach(function () {
    $this->param = 'id';
    $this->column = HiddenColumn::make($this->param);
});

it('is type hidden', function () {
    expect($this->column)
        ->getType()->toBeNull()
        ->isHidden()->toBeTrue();
});
