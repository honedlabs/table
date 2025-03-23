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
        ->builder(Product::query());
});

it('does not modify by default', function () {
    $this->table->withColumns([
        Column::make('name'),
        Column::make('price'),
    ]);

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getBuilder()->getQuery()->wheres)
        ->toBeEmpty();
});

describe('modifies', function () {
    beforeEach(function () {
        $this->columns = [
            Column::make('name'),
            Column::make('price')
                ->query(fn ($query) => $query->where('price', '>', 100)),
        ];

        $this->table->withColumns($this->columns);
    });

    it('only if cached', function () {
        $this->pipe->__invoke($this->table, $this->next);

        expect($this->table->getBuilder()->getQuery()->wheres)
            ->toBeEmpty();
    });

    it('modifies cached', function () {
        $this->table->cacheColumns($this->columns);
        $this->pipe->__invoke($this->table, $this->next);

        expect($this->table->getBuilder()->getQuery()->wheres)
            ->toHaveCount(1);
    });
});


