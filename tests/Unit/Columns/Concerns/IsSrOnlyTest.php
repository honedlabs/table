<?php

use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->column = Column::make('test');
});

it('is not screen reader only by default', function () {
    expect($this->column->isSrOnly())->toBeFalse();
    expect($this->column->isNotSrOnly())->toBeTrue();
});

it('can be set to screen reader only', function () {
    expect($this->column->srOnly())->toBeInstanceOf(Column::class)
        ->isSrOnly()->toBeTrue();
});

it('can be set to not screen reader only', function () {
    expect($this->column->srOnly(false))->toBeInstanceOf(Column::class)
        ->isSrOnly()->toBeFalse();
});

it('can be set using setter', function () {
    $this->column->setSrOnly(true);
    expect($this->column->isSrOnly())->toBeTrue();
});

it('does not accept null values', function () {
    $this->column->setSrOnly(null);
    expect($this->column->isSrOnly())->toBeFalse();
});