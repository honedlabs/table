<?php

declare(strict_types=1);

use Honed\Refine\Search;
use Honed\Table\Columns\Column;
use Honed\Table\Columns\KeyColumn;
use Honed\Table\Pipelines\Paginate;
use Honed\Table\Pipelines\ToggleColumns;
use Honed\Table\Table;
use Illuminate\Support\Facades\Request;

beforeEach(function () {
    $this->pipe = new ToggleColumns();
    $this->next = fn ($table) => $table;

    $this->columns = [
        KeyColumn::make('id'),

        Column::make('name')
            ->always(),

        Column::make('price'),

        Column::make('description')
            ->sometimes(),

        Column::make('status')
            ->sometimes(),
    ];

    $this->table = Table::make()
        ->toggle(true)
        ->withColumns($this->columns);
});

it('toggles default if not toggleable', function () {
    $this->table->toggle(false);

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getCachedColumns())
        ->toHaveCount(3);
});

it('toggles default', function () {
    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getCachedColumns())
        ->toHaveCount(3);
});

it('toggles using request', function () {
    $params = \sprintf(
        '%s%s%s', 'description', config('table.delimiter'), 'status'
    );

    $request = Request::create('/', 'GET', [
        config('table.column_key') => $params,
    ]);

    $this->table->request($request);

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getCachedColumns())
        ->toHaveCount(4);
});

it('toggles using cookie', function () {
    $this->table->remember(true);

    $request = Request::create('/', 'GET', [
        config('table.column_key') => 'description',
    ]);

    $this->table->request($request);

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getCachedColumns())
        ->toHaveCount(3);
});
