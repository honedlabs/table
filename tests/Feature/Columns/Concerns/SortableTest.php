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
            ->getQuery()->toBeNull()
            ->isHidden()->toBeTrue()
        );
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
            ->getQuery()->toBeNull()
            ->isHidden()->toBeTrue()
        );
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
            ->getQuery()->toBeInstanceOf(Closure::class)
            ->isHidden()->toBeTrue()
        );
});
