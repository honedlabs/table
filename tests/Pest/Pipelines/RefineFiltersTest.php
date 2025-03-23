<?php

declare(strict_types=1);

use Honed\Refine\Filter;
use Honed\Table\Columns\BooleanColumn;
use Honed\Table\Columns\Column;
use Honed\Table\Columns\DateColumn;
use Honed\Table\Columns\NumberColumn;
use Honed\Table\Columns\TextColumn;
use Honed\Table\Pipelines\RefineFilters;
use Honed\Table\Table;
use Honed\Table\Tests\Stubs\Product;

beforeEach(function () {
    $this->pipe = new RefineFilters;
    $this->next = fn ($table) => $table;

    $columns = [
        Column::make('name', 'Name'),
    ];

    $this->table = Table::make()
        ->builder(Product::query())
        ->withColumns($columns);
});

it('does not merge', function () {
    $this->table->withFilters(Filter::make('name'));

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table)
        ->getFilters()->toHaveCount(1)
        ->getColumns()->toHaveCount(1);
});

it('merges', function () {
    $this->table->withColumns(
        Column::make('price', 'Price')
            ->filter()
    );

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table)
        ->getFilters()->toHaveCount(1)
        ->getColumns()->toHaveCount(2);

    expect(collect($this->table->getFilters())->first())
        ->getType()->toBe('filter')
        ->getParameter()->toBe('price');
});

it('merges as date', function () {
    $this->table->withColumns(
        DateColumn::make('created_at', 'Created At')
            ->filter()
    ); 

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getFilters())->toHaveCount(1);

    expect(collect($this->table->getFilters())->first())
        ->getType()->toBe('date')
        ->getParameter()->toBe('created_at');
});

it('merges as boolean', function () {
    $this->table->withColumns(
        BooleanColumn::make('active', 'Active')
            ->filter()
    );

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table)
        ->getFilters()->toHaveCount(1)
        ->getColumns()->toHaveCount(2);

    expect(collect($this->table->getFilters())->first())
        ->getType()->toBe('boolean')
        ->getParameter()->toBe('active');
});

it('merges as number', function () {
    $this->table->withColumns(
        NumberColumn::make('price', 'Price')
            ->filter()
    );

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table)
        ->getFilters()->toHaveCount(1)
        ->getColumns()->toHaveCount(2);

    expect(collect($this->table->getFilters())->first())
        ->getType()->toBe('number')
        ->getParameter()->toBe('price');
});

it('merges as text', function () {
    $this->table->withColumns(
        TextColumn::make('description', 'Description')
            ->filter()
    );

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table)
        ->getFilters()->toHaveCount(1)
        ->getColumns()->toHaveCount(2);

    expect(collect($this->table->getFilters())->first())
        ->getType()->toBe('text')
        ->getParameter()->toBe('description');
});

