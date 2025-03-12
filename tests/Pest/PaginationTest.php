<?php

declare(strict_types=1);

use Honed\Table\Table;
use Honed\Table\PerPageRecord;
use Honed\Table\Tests\Stubs\Product;
use Honed\Table\Tests\Fixtures\Table as FixtureTable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;

beforeEach(function () {
    $this->table = FixtureTable::make();
});

it('has paginator', function () {
    $paginator = 'collection';

    // Class-based
    expect($this->table)
        ->getPaginator()->toBe(FixtureTable::Paginator)
        ->paginator($paginator)->toBe($this->table)
        ->getPaginator()->toBe($paginator);

    // Anonymous
    expect(Table::make())
        ->getPaginator()->toBe(config('table.paginator'))
        ->paginator($paginator)->toBeInstanceOf(Table::class)
        ->getPaginator()->toBe($paginator);
});

it('has pagination', function () {
    $pagination = [5, 10, 20];

    // Class based
    expect($this->table)
        ->getPagination()->toEqual(FixtureTable::Pagination)
        ->pagination($pagination)->toBe($this->table)
        ->getPagination()->toEqual($pagination);

    // Anonymous
    expect(Table::make())
        ->getPagination()->toBe(config('table.default_pagination'))
        ->pagination($pagination)->toBeInstanceOf(Table::class)
        ->getPagination()->toEqual($pagination);
});

it('has default pagination', function () {
    $default = 5;

    // Class-based
    expect($this->table)
        ->getDefaultPagination()->toBe(FixtureTable::DefaultPagination)
        ->defaultPagination($default)->toBe($this->table)
        ->getDefaultPagination()->toBe($default);

    // Anonymous
    expect(Table::make())
        ->getDefaultPagination()->toBe(config('table.default_pagination'))
        ->defaultPagination($default)->toBeInstanceOf(Table::class)
        ->getDefaultPagination()->toBe($default);
});

it('has pages key', function () {
    $pagesKey = 'on';

    // Class-based
    expect($this->table)
        ->getPagesKey()->toBe(FixtureTable::PagesKey)
        ->pagesKey($pagesKey)->toBe($this->table)
        ->getPagesKey()->toBe($pagesKey);

    // Anonymous
    expect(Table::make())
        ->getPagesKey()->toBe(config('table.pages_key'))
        ->pagesKey($pagesKey)->toBeInstanceOf(Table::class)
        ->getPagesKey()->toBe($pagesKey);
});

it('has records key', function () {
    $recordsKey = 'show';

    // Class-based
    expect($this->table)
        ->getRecordsKey()->toBe(FixtureTable::RecordsKey)
        ->recordsKey($recordsKey)->toBe($this->table)
        ->getRecordsKey()->toBe($recordsKey);

    // Anonymous
    expect(Table::make())
        ->getRecordsKey()->toBe(config('table.records_key'))
        ->recordsKey($recordsKey)->toBeInstanceOf(Table::class)
        ->getRecordsKey()->toBe($recordsKey);
});

it('has collection paginator', function () {
    $table = FixtureTable::make()
        ->paginator('collection')
        ->build();

    expect($table->getPaginationData())
        ->toBeArray()
        ->toHaveKeys([
            'empty',
        ]);
});

it('has cursor paginator', function () {
    $table = FixtureTable::make()
        ->paginator('cursor')
        ->build();

    expect($table->getPaginationData())        
        ->toBeArray()
        ->toHaveKeys([
            'prevLink',
            'nextLink',
            'perPage',
            'empty',
        ]);
});

it('has simple paginator', function () {
    $table = FixtureTable::make()
        ->paginator('simple')
        ->build();

    expect($table->getPaginationData())
        ->toBeArray()
        ->toHaveKeys([
            'currentPage',
            'prevLink',
            'nextLink',
            'perPage',
            'empty',
        ]);
});

it('has length-aware paginator', function () {
    $table = FixtureTable::make()
        ->paginator('length-aware')
        ->build();

    expect($table->getPaginationData())
        ->toBeArray()
        ->toHaveKeys([
            'total',
            'from',
            'to',
            'firstLink',
            'lastLink',
            'links',
            'currentPage',
            'prevLink',
            'nextLink',
            'perPage',
            'empty',
        ])->{'links'}->toBeArray();
});

it('creates paginate records using default', function () {
    Request::create('/', 'GET', [
        FixtureTable::RecordsKey => 1, // An invalid
    ]);

    $table = FixtureTable::make()
        ->build();

    $recordsPerPage = $table->getRecordsPerPage();

    expect($recordsPerPage)
        ->toBeArray()
        ->toHaveCount(\count(FixtureTable::Pagination));

    // Find the active one, which should be default.
    $active = collect($recordsPerPage)
        ->first(
            fn (PerPageRecord $record) => $record->getValue() 
                === FixtureTable::DefaultPagination
        );

    expect($active)
        ->isActive()->toBeTrue();
});

it('creates paginate records using dynamics', function () {
    $pagination = Arr::last(FixtureTable::Pagination);

    $request = Request::create('/', 'GET', [
        FixtureTable::RecordsKey => $pagination,
    ]);

    $table = FixtureTable::make()
        ->request($request)
        ->build();

    $recordsPerPage = $table->getRecordsPerPage();

    expect($recordsPerPage)
        ->toBeArray()
        ->toHaveCount(\count(FixtureTable::Pagination));

    // Find the active one, which should be default.
    $active = collect($recordsPerPage)
        ->first(
            fn (PerPageRecord $record) => $record->getValue() === $pagination
        );

    expect($active)
        ->isActive()->toBeTrue();

    expect($table->getPaginationData())
        ->{'perPage'}->toBe($pagination);
});