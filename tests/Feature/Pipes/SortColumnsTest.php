<?php

declare(strict_types=1);

use Honed\Refine\Sorts\Sort;
use Honed\Table\Columns\NumericColumn;
use Honed\Table\Columns\TextColumn;
use Honed\Table\Pipes\SortColumns;
use Honed\Table\Table;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->pipe = new SortColumns();

    $this->table = Table::make()
        ->for(Product::class)
        ->columns(NumericColumn::make('price')->sortable());
});

it('creates', function () {
    $this->pipe->through($this->table);

    expect($this->table->getSorts())
        ->toHaveCount(1)
        ->{0}
        ->scoped(fn ($sort) => $sort
            ->toBeInstanceOf(Sort::class)
            ->getName()->toBe('price')
            ->getLabel()->toBe('Price')
        );
});

it('does not create if column has no sort', function () {
    $this->pipe->through($this->table
        ->columns(TextColumn::make('name'))
    );

    expect($this->table->getSorts())->toHaveCount(1);
});

it('does not create if not active', function () {
    $this->pipe->through($this->table
        ->columns(NumericColumn::make('price')->active(false))
    );

    expect($this->table->getSorts())->toHaveCount(1);
});

it('does not create if table is not sortable', function () {
    $this->pipe->through($this->table->sortable(false));

    expect($this->table->getSorts())->toBeEmpty();
});
