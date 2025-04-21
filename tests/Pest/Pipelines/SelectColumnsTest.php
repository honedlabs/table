<?php

declare(strict_types=1);

use Honed\Refine\Search;
use Honed\Table\Columns\Column;
use Honed\Table\Pipelines\Paginate;
use Honed\Table\Pipelines\SelectColumns;
use Honed\Table\Table;
use Honed\Table\Tests\Stubs\Product;
use Illuminate\Support\Facades\Request;

beforeEach(function () {
    $this->pipe = new SelectColumns();
    $this->next = fn ($table) => $table;

    $this->table = Table::make()
        ->resource(Product::query())
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

it('selects only if select', function () {
    $this->table->selects(false);

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getResource()->getQuery()->columns)
        ->toBeEmpty();
});

it('selects', function () {
    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getResource()->getQuery()->columns)
        ->toEqual([
            'name',
            'price',
            'cost',
            'description as content',
        ]);
});

