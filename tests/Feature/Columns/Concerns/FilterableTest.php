<?php

declare(strict_types=1);

use Honed\Refine\Filters\Filter;
use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->column = Column::make('name');
});

it('is filterable', function () {
    expect($this->column)
        ->isNotFilterable()->toBeTrue()
        ->isFilterable()->toBeFalse()
        ->getFilter()->toBeNull()
        ->filterable()->toBe($this->column)
        ->isFilterable()->toBeTrue()
        ->getFilter()
        ->scoped(fn ($filter) => $filter
            ->toBeInstanceOf(Filter::class)
            ->getName()->toBe('name')
            ->getLabel()->toBe('Name')
            ->isHidden()->toBeFalse()
        )
        ->notFilterable()->toBe($this->column)
        ->isNotFilterable()->toBeTrue()
        ->getFilter()->toBeNull();
});

it('is filterable with closure', function () {
    $this->column->filterable(fn () => 'value');

    expect($this->column)
        ->filterable(fn ($query) => $query->where('name', 'value'))->toBe($this->column)
        ->isFilterable()->toBeTrue()
        ->getFilter()
        ->scoped(fn ($filter) => $filter
            ->toBeInstanceOf(Filter::class)
            ->getLabel()->toBe('Name')
            ->queryCallback()->toBeInstanceOf(Closure::class)
        );
});

it('infers filter type', function ($type, $as) {
    expect($this->column->type($type)->filterable()->getFilter())
        ->toBeInstanceOf(Filter::class)
        ->interpretsAs()->toBe($as);
})->with([
    [Column::ARRAY, 'array'],
    [Column::BOOLEAN, 'boolean'],
    [Column::DATE, 'date'],
    [Column::DATETIME, 'datetime'],
    [Column::TIME, 'time'],
    [Column::NUMERIC, 'int'],
    [Column::TEXT, 'string'],
]);

it('creates filter with alias', function () {
    $this->column->alias('alias');

    expect($this->column->filterable()->getFilter())
        ->toBeInstanceOf(Filter::class)
        ->getAlias()->toBe('alias')
        ->isHidden()->toBeFalse()
        ->interpretsAs()->toBeNull();
});
