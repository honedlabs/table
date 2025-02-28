<?php

declare(strict_types=1);

use Honed\Table\Table;
use Honed\Table\Tests\Fixtures\Table as FixtureTable;

beforeEach(function () {
    $this->table = FixtureTable::make();
});

it('has pagination', function () {
    expect($this->table)
        ->getPagination()->toEqual(FixtureTable::Pagination);

    $p = [10, 50, 100];

    expect(Table::make())
        ->getPagination()->toBe(config('table.pagination.default'))
        ->pagination($p)
        ->getPagination()->toBe($p);
});

it('has default pagination', function () {
    expect($this->table)
        ->getDefault()->toBe(FixtureTable::DefaultPagination);

    expect(Table::make())
        ->getDefault()->toBe(config('table.pagination.default'));
});

it('can be length-aware', function () {
    populate();

    expect($this->table)
        ->getPaginator()->toBe('length-aware')
        ->build()->toBe($this->table)
        ->getRecords()->scoped(fn ($records) => $records
        ->toBeArray()
        ->toHaveCount(FixtureTable::DefaultPagination)
        ->each->toHaveKeys([
            'id',
            'name',
            'description',
            'best_seller',
            'status',
            'price',
            'actions',
        ])
        )
        ->getMeta()->toHaveKeys([
            'prev',
            'current',
            'next',
            'per_page',
            'total',
            'from',
            'to',
            'first',
            'last',
            'links',
        ]);

    expect(Table::make())
        ->getPaginator()->toBe('length-aware');
});

it('can be simple', function () {
    expect($this->table->paginator('simple')->build())
        ->getPaginator()->toBe('simple')
        ->getMeta()->toHaveKeys([
            'prev',
            'current',
            'next',
            'per_page',
        ]);
});

it('can be cursor', function () {
    expect($this->table->paginator('cursor')->build())
        ->getPaginator()->toBe('cursor')
        ->getMeta()->toHaveKeys([
            'prev',
            'per_page',
            'next',
        ]);
});

it('can be collection', function () {
    expect($this->table->paginator('collection')->build())
        ->getPaginator()->toBe('collection')
        ->getMeta()->toBeEmpty();
});

it('throws exception when invalid paginator', function () {
    expect(fn () => $this->table->paginator('invalid')->build())
        ->toThrow(\InvalidArgumentException::class);
});

it('can set the page key', function () {
    expect($this->table)
        ->getPagesKey()->toBe(FixtureTable::PagesKey)
        ->pagesKey('test')
        ->getPagesKey()->toBe('test');

    expect(Table::make())
        ->getPagesKey()->toBe(config('table.config.pages'))
        ->pagesKey('test')
        ->getPagesKey()->toBe('test');
});
