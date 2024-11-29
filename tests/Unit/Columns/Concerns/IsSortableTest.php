<?php

use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->column = Column::make('test');
});

it('is not sortable by default', function () {
    expect($this->column->isSortable())->toBeFalse();
    expect($this->column->isNotSortable())->toBeTrue();
});

it('can be set to sortable', function () {
    expect($this->column->sortable())->toBeInstanceOf(Column::class)
        ->isSortable()->toBeTrue();
});

it('can be set to not sortable', function () {
    expect($this->column->sortable(false))->toBeInstanceOf(Column::class)
        ->isSortable()->toBeFalse();
});

it('can be set using setter', function () {
    $this->column->setSortable(true);
    expect($this->column->isSortable())->toBeTrue();
});

it('does not accept null values', function () {
    $this->column->setSortable(null);
    expect($this->column->isSortable())->toBeFalse();
});