<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;
use Honed\Table\Pipelines\SelectColumns;
use Honed\Table\Table;
use Honed\Table\Tests\Stubs\Product;

beforeEach(function () {
    $this->pipe = new SelectColumns();

    $this->next = fn ($table) => $table;
    
    $this->builder = Product::query();

    $this->table = Table::make()
        ->resource($this->builder)
        ->selects(true)
        ->cacheColumns([
            Column::make('name')
                ->selects(),
            Column::make('price')
                ->selects(['price', 'cost']),
            Column::make('description')
                ->selects('description as content'),
            Column::make('status')
                ->selects(false),
        ]);
});

it('selects', function () {
    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getResource()->getQuery()->columns)
        ->toEqual([
            $this->builder->qualifyColumn('name'),
            $this->builder->qualifyColumn('price'),
            $this->builder->qualifyColumn('cost'),
            $this->builder->qualifyColumn('description as content'),
        ]);
});

