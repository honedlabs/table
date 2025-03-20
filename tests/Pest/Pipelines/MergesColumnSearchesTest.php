<?php

declare(strict_types=1);

use Honed\Refine\Search;
use Honed\Table\Columns\Column;
use Honed\Table\Pipelines\MergeColumnSearches;
use Honed\Table\Table;

beforeEach(function () {
    $this->pipe = new MergeColumnSearches();
    $this->next = fn ($table) => $table;

    $columns = [
        Column::make('name', 'Name'),
    ];

    $this->table = Table::make()
        ->withColumns($columns);
});

it('does not merge', function () {
    $this->table->withSearches(Search::make('name'));

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table)
        ->getSearches()->toHaveCount(1)
        ->getColumns()->toHaveCount(1);
});

it('merges', function () {
    $this->table->withColumns(
        Column::make('description')
            ->search()
    );

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table)
        ->getSearches()->toHaveCount(1)
        ->getColumns()->toHaveCount(2);

    expect(collect($this->table->getSearches())->first())
        ->getType()->toBe('search')
        ->getParameter()->toBe('description');
});