<?php

declare(strict_types=1);

use Honed\Table\Columns\HiddenColumn;

beforeEach(function () {
    $this->param = 'id';
    $this->column = HiddenColumn::make($this->param);
});

it('makes', function () {
    expect($this->column)
        ->getName()->toBe($this->param)
        ->getLabel()->toBe(ucfirst($this->param))
        ->isAlways()->toBeTrue()
        ->isHidden()->toBeTrue()
        ->getType()->toBe('hidden');
});