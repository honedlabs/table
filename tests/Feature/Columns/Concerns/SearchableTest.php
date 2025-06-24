<?php

declare(strict_types=1);

use Honed\Refine\Searches\Search;
use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->column = Column::make('name');
});

it('is searchable', function () {
    expect($this->column)
        ->isSearchable()->toBeFalse()
        ->getSearch()->toBeNull()
        ->searchable()->toBe($this->column)
        ->isSearchable()->toBeTrue()
        ->getSearch()
        ->scoped(fn ($search) => $search
            ->toBeInstanceOf(Search::class)
            ->getName()->toBe($this->column->getName())
            ->getLabel()->toBe('Name')
            ->getQuery()->toBeNull()
        );
});

it('is searchable with string', function () {
    $this->column->searchable('description');

    expect($this->column)
        ->isSearchable()->toBeTrue()
        ->getSearch()->toBeInstanceOf(Search::class)
        ->scoped(fn ($search) => $search
            ->toBeInstanceOf(Search::class)
            ->getName()->toBe('description')
            ->getLabel()->toBe('Name')
            ->getQuery()->toBeNull()
        );
});

it('is searchable with closure', function () {
    $this->column->searchable(fn () => 'value');

    expect($this->column)
        ->searchable(fn ($query) => $query->where('name', 'value'))->toBe($this->column)
        ->isSearchable()->toBeTrue()
        ->getSearch()
        ->scoped(fn ($search) => $search
            ->toBeInstanceOf(Search::class)
            ->getName()->toBe($this->column->getName())
            ->getLabel()->toBe('Name')
            ->getQuery()->toBeInstanceOf(Closure::class)
        );
});
