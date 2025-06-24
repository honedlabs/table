<?php

declare(strict_types=1);

use Honed\Refine\Filters\Filter;
use Honed\Refine\Sorts\Sort;
use Honed\Table\Columns\NumericColumn;
use Honed\Table\Columns\TextColumn;
use Honed\Table\Pipes\PrepareColumns;
use Honed\Table\Table;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->pipe = new PrepareColumns();

    $this->table = Table::make()->for(Product::class);
});

describe('searches', function () {
    beforeEach(function () {
        $this->table->columns(TextColumn::make('name')->searchable());
    });

    it('creates', function () {
        $this->pipe->run($this->table);

        expect($this->table->getSearches())->toHaveCount(1);
    });

    it('does not create if column has no search', function () {
        $this->pipe->run(
            $this->table
                ->columns(NumericColumn::make('price'))
        );

        expect($this->table->getSearches())->toHaveCount(1);
    });

    it('does not create if table is not searchable', function () {
        $this->pipe->run(
            $this->table->searchable(false)
        );

        expect($this->table->getSearches())->toBeEmpty();
    });
});

describe('filters', function () {
    beforeEach(function () {
        $this->table->columns(NumericColumn::make('price')->filterable());
    });

    it('creates', function () {
        $this->pipe->run($this->table);

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
        $this->pipe->run(
            $this->table->filterable(false)
        );

        expect($this->table->getFilters())->toBeEmpty();
    });

    it('does not create if column has no filter', function () {
        $this->pipe->run(
            $this->table
                ->columns(TextColumn::make('name'))
        );

        expect($this->table->getFilters())->toHaveCount(1);
    });
});

describe('sorts', function () {
    beforeEach(function () {
        $this->table->columns(NumericColumn::make('price')->sortable());
    });

    it('creates', function () {
        $this->pipe->run($this->table);

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
        $this->pipe->run(
            $this->table
                ->columns(TextColumn::make('name'))
        );

        expect($this->table->getSorts())->toHaveCount(1);
    });

    it('does not create if not active', function () {
        $this->pipe->run(
            $this->table
                ->columns(NumericColumn::make('price')->active(false))
        );

        expect($this->table->getSorts())->toHaveCount(1);
    });

    it('does not create if table is not sortable', function () {
        $this->pipe->run(
            $this->table->sortable(false)
        );

        expect($this->table->getSorts())->toBeEmpty();
    });
});

describe('selects', function () {
    beforeEach(function () {
        $this->table->columns(NumericColumn::make('price')->selectable());

        expect($this->table)
            ->isSelectable()->toBeFalse();
    });

    it('selects', function () {
        $this->pipe->run(
            $this->table->selectable()
        );

        expect($this->table)
            ->isSelectable()->toBeTrue()
            ->getSelects()->toBe(['price']);
    });

    it('does not select if not active', function () {
        $this->pipe->run(
            $this->table
                ->selectable()
                ->columns(NumericColumn::make('price')->active(false))
        );

        expect($this->table)
            ->isSelectable()->tobeTrue()
            ->getSelects()->toEqual(['price']);
    });

    it('does not select if not selectable', function () {
        $this->pipe->run(
            $this->table
                ->selectable(false)
        );

        expect($this->table)
            ->isSelectable()->toBeFalse()
            ->getSelects()->toBeEmpty();
    });
});
