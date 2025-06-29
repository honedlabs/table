<?php

declare(strict_types=1);

use Honed\Refine\Filters\Filter;
use Honed\Refine\Searches\Search;
use Honed\Refine\Sorts\Sort;
use Honed\Table\Columns\Column;
use Honed\Table\Facades\Views;
use Honed\Table\Table;
use Illuminate\Http\Request;
use Workbench\App\Enums\Status;
use Workbench\App\Models\Product;
use Workbench\App\Tables\ProductTable;

beforeEach(function () {
    $this->table = Table::make()
        ->for(Product::class)
        ->columns([Column::make('name'), Column::make('price')])
        ->filter(Filter::make('name'))
        ->search(Search::make('name'))
        ->sort(Sort::make('price'))
        ->filterable()
        ->searchable()
        ->sortable()
        ->matchable()
        ->toggleable();

    $this->request = Request::create('/', 'GET', [
        'name' => 'test',
        'missing' => 'test',
        $this->table->getSortKey() => '-price',
        $this->table->getMatchKey() => 'name',
        $this->table->getSearchKey() => 'search+term',
        $this->table->getColumnKey() => 'name,description,price',
        $this->table->getRecordKey() => 25,
    ]);

    $this->table->request($this->request);
});

it('creates state', function () {
    $this->table->build();

    expect($this->table->toState())
        ->toHaveKeys([
            'name',
            $this->table->getSortKey(),
            $this->table->getMatchKey(),
            $this->table->getSearchKey(),
            $this->table->getColumnKey(),
        ])
        ->{'sort'}->toBe('price') // @TODO: Fix this
        ->{'search'}->toBe('search+term')
        ->{'columns'}->toBe('name,price');

    expect($this->table->toState()['match'])->toBe('name');
});

it('can remove state', function ($has, $missing) {
    expect($this->table->toState())
        ->toHaveKeys($has)
        ->not->toHaveKeys($missing);
})->with([
    'not sortable' => function () {
        $this->table->notSortable();

        return [[
            'name',
            $this->table->getColumnKey(),
            $this->table->getMatchKey(),
            $this->table->getSearchKey(),
        ], [
            $this->table->getSortKey(),
        ]];
    },
    'not searchable' => function () {
        $this->table->notSearchable();

        return [[
            'name',
            $this->table->getSortKey(),
            $this->table->getColumnKey(),
        ], [
            $this->table->getMatchKey(),
            $this->table->getSearchKey(),
        ]];
    },
    'not matchable' => function () {
        $this->table->notMatchable();

        return [[
            'name',
            $this->table->getSortKey(),
            $this->table->getColumnKey(),
            $this->table->getSearchKey(),
        ], [
            $this->table->getMatchKey(),
        ]];
    },
    'not filterable' => function () {
        $this->table->notFilterable();

        return [[
            $this->table->getSortKey(),
            $this->table->getSearchKey(),
            $this->table->getMatchKey(),
            $this->table->getColumnKey(),
        ], [
            'name',
        ]];
    },
    'not toggleable' => function () {
        $this->table->notToggleable();

        return [[
            'name',
            $this->table->getSortKey(),
            $this->table->getMatchKey(),
            $this->table->getSearchKey(),
        ], [
            $this->table->getColumnKey(),
        ]];
    },
]);