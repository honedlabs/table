<?php

use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->column = Column::make('test');
});

it('is not searchable by default', function () {
    expect($this->column->isSearchable())->toBeFalse();
    expect($this->column->isNotSearchable())->toBeTrue();
});

it('can be set to searchable', function () {
    expect($this->column->searchable())->toBeInstanceOf(Column::class)
        ->isSearchable()->toBeTrue();
});

it('can be set to not searchable', function () {
    expect($this->column->searchable(false))->toBeInstanceOf(Column::class)
        ->isSearchable()->toBeFalse();
});

it('can be set using setter', function () {
    $this->column->setSearchable(true);
    expect($this->column->isSearchable())->toBeTrue();
});

it('does not accept null values', function () {
    $this->column->setSearchable(null);
    expect($this->column->isSearchable())->toBeFalse();
});