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
});

describe('filters', function () {
    beforeEach(function () {
        $this->table->columns(NumericColumn::make('price')->filterable());
    });

    it('creates', function () {
        $this->pipe->instance($this->table);

        $this->pipe->run();

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
        $this->pipe->instance($this->table->filterable(false));

        $this->pipe->run();

        expect($this->table->getFilters())->toBeEmpty();
    });

    it('does not create if column has no filter', function () {
        $this->pipe->instance($this->table
            ->columns(TextColumn::make('name'))
        );

        $this->pipe->run();

        expect($this->table->getFilters())->toHaveCount(1);
    });
});

describe('sorts', function () {
    beforeEach(function () {
        $this->table->columns(NumericColumn::make('price')->sortable());
    });

    it('creates', function () {
        $this->pipe->instance($this->table);

        $this->pipe->run();

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
        $this->pipe->instance($this->table
            ->columns(TextColumn::make('name'))
        );

        $this->pipe->run();

        expect($this->table->getSorts())->toHaveCount(1);
    });

    it('does not create if not active', function () {
        $this->pipe->instance($this->table
            ->columns(NumericColumn::make('price')->active(false))
        );

        $this->pipe->run();

        expect($this->table->getSorts())->toHaveCount(1);
    });

    it('does not create if table is not sortable', function () {

        $this->pipe->instance($this->table->sortable(false));

        $this->pipe->run();

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
        $this->pipe->instance($this->table->selectable());

        $this->pipe->run();

        expect($this->table)
            ->isSelectable()->toBeTrue()
            ->getSelects()->toBe(['price']);
    });

    it('does not select if not active', function () {
        $this->pipe->instance($this->table
            ->selectable()
            ->columns(NumericColumn::make('price')->active(false))
        );

        $this->pipe->run();

        expect($this->table)
            ->isSelectable()->tobeTrue()
            ->getSelects()->toEqual(['price']);
    });

    it('does not select if not selectable', function () {
        $this->pipe->instance($this->table
            ->selectable(false)
        );

        $this->pipe->run();

        expect($this->table)
            ->isSelectable()->toBeFalse()
            ->getSelects()->toBeEmpty();
    });
});
