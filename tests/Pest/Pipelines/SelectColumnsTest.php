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
        ->for(Product::query())
        ->select(true)
        ->cacheColumns([
            Column::make('name')
                ->select(),
            Column::make('price')
                ->select(['price', 'cost']),
            Column::make('description')
                ->select('description as content'),
            Column::make('status')
                ->select(false),
        ]);
});

it('selects only if select', function () {
    $this->table->select(false);

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getFor()->getQuery()->columns)
        ->toBeEmpty();
});

it('selects', function () {
    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getFor()->getQuery()->columns)
        ->toEqual([
            'name',
            'price',
            'cost',
            'description as content',
        ]);
});

