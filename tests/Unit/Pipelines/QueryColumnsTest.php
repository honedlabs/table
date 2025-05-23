<?php

declare(strict_types=1);

use Honed\Refine\Search;
use Honed\Table\Columns\Column;
use Honed\Table\Pipelines\Paginate;
use Honed\Table\Pipelines\QueryColumns;
use Honed\Table\Table;
use Honed\Table\Tests\Stubs\Product;
use Illuminate\Support\Facades\Request;

beforeEach(function () {
    $this->pipe = new QueryColumns();
    $this->next = fn ($table) => $table;

    $this->table = Table::make()
        ->resource(Product::query());
});

it('does not modify by default', function () {
    $this->table->columns([
        Column::make('name'),
        Column::make('price'),
    ]);

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getResource()->getQuery()->wheres)
        ->toBeEmpty();
});

describe('modifies', function () {
    beforeEach(function () {
        $this->columns = [
            Column::make('name'),
            Column::make('price')
                ->query(fn ($query) => $query->where('price', '>', 100)),
        ];

        $this->table->columns($this->columns);
    });

    it('only if cached', function () {
        $this->pipe->__invoke($this->table, $this->next);

        expect($this->table->getResource()->getQuery()->wheres)
            ->toBeEmpty();
    });

    it('modifies cached', function () {
        $this->table->cacheColumns($this->columns);
        $this->pipe->__invoke($this->table, $this->next);

        expect($this->table->getResource()->getQuery()->wheres)
            ->toHaveCount(1);
    });
});


