<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;
use Honed\Table\Table as BaseTable;
use Honed\Table\Tests\Fixtures\Table;

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

it('has refineable columns', function () {
    expect($this->table)
        ->getSortableColumns()->toHaveCount(2)
        ->getSearchableColumns()->toHaveCount(1)
        ->getKeyColumn()->scoped(fn ($column) => $column
        ->getName()->toBe('id')
        ->isKey()->toBeTrue()
        );
});
