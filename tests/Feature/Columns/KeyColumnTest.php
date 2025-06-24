<?php

declare(strict_types=1);

use Honed\Table\Columns\KeyColumn;

beforeEach(function () {
    $this->column = KeyColumn::make('id');
});

it('is key', function () {
    expect($this->column)
        ->getType()->toBeNull()
        ->isKey()->toBeTrue()
        ->isHidden()->toBeTrue()
        ->isQualifying()->toBeTrue();
});
