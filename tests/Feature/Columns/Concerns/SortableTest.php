<?php

declare(strict_types=1);

use Honed\Refine\Sorts\Sort;
use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->column = Column::make('name');
});

it('is sortable', function () {
    expect($this->column)
        ->isSortable()->toBeFalse()
        ->getSort()->toBeNull()
        ->sortable()->toBe($this->column)
        ->isSortable()->toBeTrue()
        ->getSort()
        ->scoped(fn ($sort) => $sort
            ->toBeInstanceOf(Sort::class)
            ->getName()->toBe($this->column->getName())
            ->getLabel()->toBe('Name')
            ->queryCallback()->toBeNull()
            ->isHidden()->toBeTrue()
        )
        ->notSortable()->toBe($this->column)
        ->isNotSortable()->toBeTrue()
        ->getSort()->toBeNull();
});

it('is sortable with string', function () {
    $this->column->sortable('description');

    expect($this->column)
        ->isSortable()->toBeTrue()
        ->getSort()->toBeInstanceOf(Sort::class)
        ->scoped(fn ($sort) => $sort
            ->toBeInstanceOf(Sort::class)
            ->getName()->toBe('description')
            ->getLabel()->toBe('Name')
            ->queryCallback()->toBeNull()
            ->isHidden()->toBeTrue()
        );
});

it('is sortable with sort instance', function () {
    $this->column->sortable(Sort::make('name', 'Name'));

    expect($this->column)
        ->sortable(Sort::make('name', 'Name'))->toBe($this->column)
        ->isSortable()->toBeTrue()
        ->getSort()->toBeInstanceOf(Sort::class);
});

it('is sortable with closure', function () {
    $this->column->sortable(fn () => 'value');

    expect($this->column)
        ->sortable(fn ($query) => $query->where('name', 'value'))->toBe($this->column)
        ->isSortable()->toBeTrue()
        ->getSort()
        ->scoped(fn ($sort) => $sort
            ->toBeInstanceOf(Sort::class)
            ->getName()->toBe($this->column->getName())
            ->getLabel()->toBe('Name')
            ->queryCallback()->toBeInstanceOf(Closure::class)
            ->isHidden()->toBeTrue()
        );
});
