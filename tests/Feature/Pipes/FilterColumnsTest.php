<?php

declare(strict_types=1);

use Honed\Refine\Filters\Filter;
use Honed\Table\Columns\NumericColumn;
use Honed\Table\Columns\TextColumn;
use Honed\Table\Pipes\FilterColumns;
use Honed\Table\Table;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->pipe = new FilterColumns();

    $this->table = Table::make()
        ->for(Product::class)
        ->columns(NumericColumn::make('price')->filterable());
});

it('creates', function () {
    $this->pipe->through($this->table);

    expect($this->table->getFilters())
        ->toHaveCount(1)
        ->{0}
        ->scoped(fn ($filter) => $filter
            ->toBeInstanceOf(Filter::class)
            ->getName()->toBe('price')
            ->getLabel()->toBe('Price')
            ->interpretsAs()->toBe('int')
        );
});

it('does not create if table is not filterable', function () {
    $this->pipe->through($this->table->filterable(false));

    expect($this->table->getFilters())->toBeEmpty();
});

it('does not create if column has no filter', function () {
    $this->pipe->through($this->table
        ->columns(TextColumn::make('name'))
    );

    expect($this->table->getFilters())->toHaveCount(1);
});
