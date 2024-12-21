<?php

use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->column = Column::make('test');
});

it('is not toggleable by default', function () {
    expect($this->column->isToggleable())->toBeFalse();
    expect($this->column->isNotToggleable())->toBeTrue();
});

it('can be set to toggleable', function () {
    expect($this->column->toggleable())->toBeInstanceOf(Column::class)
        ->isToggleable()->toBeTrue();
});

it('can be set to not toggleable', function () {
    expect($this->column->toggleable(false))->toBeInstanceOf(Column::class)
        ->isToggleable()->toBeFalse();
});

it('can be set using setter', function () {
    $this->column->setToggleable(true);
    expect($this->column->isToggleable())->toBeTrue();
});

it('does not accept null values', function () {
    $this->column->setToggleable(null);
    expect($this->column->isToggleable())->toBeFalse();
});
