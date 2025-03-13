<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;
use Honed\Table\Table as BaseTable;
use Honed\Table\Tests\Fixtures\Table;
use Honed\Table\Columns\KeyColumn;

beforeEach(function () {
    $this->table = Table::make();
});

it('has columns', function () {
    expect($this->table)
        ->hasColumns()->toBeTrue()
        ->getColumns()->toHaveCount(9)
        ->getActiveColumns()->toHaveCount(9);

    expect(BaseTable::make())
        ->columns([Column::make('id')->allow(false), Column::make('public_id')])
        ->getColumns()->toHaveCount(1);
});

it('has sortable columns', function () {
    expect($this->table)
        ->getColumnSorts()->toHaveCount(2)
        ->each(fn ($column) => $column
            ->toBeInstanceOf(Column::class)
            ->isSortable()->toBeTrue()
        );

    expect(BaseTable::make())
        ->getColumnSorts()->toBeEmpty();
});

it('has searchable columns', function () {
    expect($this->table)
        ->getColumnSearches()->toHaveCount(1)
        ->each(fn ($column) => $column
            ->toBeInstanceOf(Column::class)
            ->isSearchable()->toBeTrue()
        );

    expect(BaseTable::make())
        ->getColumnSearches()->toBeEmpty();
});

it('has a key column', function () {
    expect($this->table->getKeyColumn())
        ->toBeInstanceOf(KeyColumn::class)
        ->getName()->toBe('id')
        ->isKey()->toBeTrue();

    expect(BaseTable::make()->getKeyColumn())
        ->toBeNull();
});

it('can disable columns', function () {
    expect($this->table)
        ->isWithoutColumns()->toBeFalse()
        ->getColumns()->toHaveCount(9)
        ->columnsToArray()->toHaveCount(9)
        ->withoutColumns()->toBe($this->table)
        ->isWithoutColumns()->toBeTrue()
        ->getColumns()->toHaveCount(9)
        ->columnsToArray()->toBeEmpty();
});
