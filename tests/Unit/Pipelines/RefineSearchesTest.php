<?php

declare(strict_types=1);

use Honed\Refine\Search;
use Honed\Table\Columns\Column;
use Honed\Table\Pipelines\RefineSearches;
use Honed\Table\Table;
use Honed\Table\Tests\Stubs\Product;

beforeEach(function () {
    $this->pipe = new RefineSearches;
    $this->next = fn ($table) => $table;

    $columns = [
        Column::make('name', 'Name'),
    ];

    $this->table = Table::make()
        ->resource(Product::query())
        ->columns($columns);
});

it('does not merge', function () {
    $this->table->searches(Search::make('name'));

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table)
        ->getSearches()->toHaveCount(1)
        ->getColumns()->toHaveCount(1);
});

it('merges', function () {
    $this->table->columns(
        Column::make('description')
            ->searches()
    );

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table)
        ->getSearches()->toHaveCount(1)
        ->getColumns()->toHaveCount(2);

    expect(collect($this->table->getSearches())->first())
        ->getType()->toBe('search')
        ->getParameter()->toBe('description');
});