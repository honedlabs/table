<?php

declare(strict_types=1);

use Honed\Table\Columns\KeyColumn;

beforeEach(function () {
    $this->param = 'public_id';
    $this->column = KeyColumn::make($this->param);
});

it('makes', function () {
    expect($this->column)
        ->getName()->toBe($this->param)
        ->getLabel()->toBe('Public id')
        ->isHidden()->toBeTrue()
        ->getType()->toBe('key');
});