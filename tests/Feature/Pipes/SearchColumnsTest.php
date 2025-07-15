<?php

declare(strict_types=1);

use Honed\Table\Columns\NumericColumn;
use Honed\Table\Columns\TextColumn;
use Honed\Table\Pipes\SearchColumns;
use Honed\Table\Table;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->pipe = new SearchColumns();

    $this->table = Table::make()
        ->for(Product::class)
        ->columns(TextColumn::make('name')->searchable());
});

it('creates', function () {
    $this->pipe->instance($this->table);

    $this->pipe->run();

    expect($this->table->getSearches())->toHaveCount(1);
});

it('does not create if column has no search', function () {
    $this->pipe->instance($this->table
        ->columns(NumericColumn::make('price'))
    );

    $this->pipe->run();

    expect($this->table->getSearches())->toHaveCount(1);
});

it('does not create if table is not searchable', function () {
    $this->pipe->instance($this->table->searchable(false));

    $this->pipe->run();

    expect($this->table->getSearches())->toBeEmpty();
});
