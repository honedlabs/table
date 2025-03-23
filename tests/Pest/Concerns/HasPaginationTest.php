<?php

declare(strict_types=1);

use Honed\Table\Table;
use Honed\Table\PerPageRecord;
use Honed\Table\Tests\Stubs\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;

beforeEach(function () {
    $this->table = Table::make();
});

it('has paginator', function () {
    expect($this->table)
        ->getPaginator()->toBe(config('table.paginator'))
        ->paginator('cursor')->toBe($this->table)
        ->getPaginator()->toBe('cursor')
        ->getDefaultPaginator()->toBe(config('table.paginator'));
});

it('has pagination', function () {
    expect($this->table)
        ->getPagination()->toBe(config('table.pagination'))
        ->pagination([5, 10, 20])->toBe($this->table)
        ->getPagination()->toEqual([5, 10, 20])
        ->getDefaultPagination()->toBe(config('table.pagination'));
});

it('has default pagination', function () {
    expect($this->table)
        ->getDefaultPagination()->toBe(config('table.default_pagination'))
        ->defaultPagination(5)->toBe($this->table)
        ->getDefaultPagination()->toBe(5)
        ->getDefaultedPagination()->toBe(config('table.default_pagination'));
});

it('has pages key', function () {
    expect($this->table)
        ->getPageKey()->toBe(config('table.page_key'))
        ->pageKey('test')->toBe($this->table)
        ->getPageKey()->toBe('test')
        ->getDefaultPageKey()->toBe(config('table.page_key'));
});

it('has records key', function () {
    expect($this->table)
        ->getRecordKey()->toBe(config('table.record_key'))
        ->recordKey('test')->toBe($this->table)
        ->getRecordKey()->toBe('test')
        ->getDefaultRecordKey()->toBe(config('table.record_key'));
});

it('has window', function () {
    expect($this->table)
        ->getWindow()->toBe(config('table.window'))
        ->window(5)->toBe($this->table)
        ->getWindow()->toBe(5)
        ->fallbackWindow()->toBe(config('table.window'));
});

it('has records per page', function () {
    expect($this->table)
        ->getRecordsPerPage()->toBeEmpty();

    $this->table->createRecordsPerPage([5, 10, 20], 20);

    expect($this->table->getRecordsPerPage())
        ->toBeArray()
        ->toHaveCount(3)
        ->{0}->scoped(fn ($perPage) => $perPage
            ->toBeInstanceOf(PerPageRecord::class)
            ->getValue()->toBe(5)
            ->isActive()->toBeFalse()
        )
        ->{1}->scoped(fn ($perPage) => $perPage
            ->toBeInstanceOf(PerPageRecord::class)
            ->getValue()->toBe(10)
            ->isActive()->toBeFalse()
        )
        ->{2}->scoped(fn ($perPage) => $perPage
            ->toBeInstanceOf(PerPageRecord::class)
            ->getValue()->toBe(20)
            ->isActive()->toBeTrue()
        );

    expect($this->table)
        ->recordsPerPageToArray()->toEqual([
            [
                'value' => 5,
                'active' => false,
            ],
            [
                'value' => 10,
                'active' => false,
            ],
            [
                'value' => 20,
                'active' => true,
            ],
        ]);
});

it('checks length-aware paginator', function () {
    expect($this->table->isLengthAware('length-aware'))->toBeTrue();
    expect($this->table->isLengthAware('cursor'))->toBeFalse();
});

it('checks simple paginator', function () {
    expect($this->table->isSimple('simple'))->toBeTrue();
    expect($this->table->isSimple('cursor'))->toBeFalse();
});


it('checks cursor paginator', function () {
    expect($this->table->isCursor('cursor'))->toBeTrue();
    expect($this->table->isCursor('simple'))->toBeFalse();
});

it('checks collection paginator', function () {
    expect($this->table->isCollector('collection'))->toBeTrue();
    expect($this->table->isCollector('cursor'))->toBeFalse();
});


it('paginates collection', function () {
    $paginated = Product::query()->get();

    expect($this->table->collectionPaginator($paginated))
        ->toBeArray()
        ->toHaveKeys(['empty']);
});

it('paginates cursor', function () {
    $paginated = Product::query()->cursorPaginate();

    expect($this->table->cursorPaginator($paginated))
        ->toBeArray()
        ->toHaveKeys([
            'empty',
            'prevLink',
            'nextLink',
            'perPage',
        ]);
});

it('paginates simple', function () {
    $paginated = Product::query()->simplePaginate();

    expect($this->table->simplePaginator($paginated))
        ->toBeArray()
        ->toHaveKeys([
            'empty',
            'prevLink',
            'nextLink',
            'perPage',
            'currentPage',
        ]);
});

it('paginates length-aware', function () {
    $paginated = Product::query()->paginate();

    expect($this->table->lengthAwarePaginator($paginated))
        ->toBeArray()
        ->toHaveKeys([
            'empty',
            'prevLink',
            'nextLink',
            'perPage',
            'currentPage',
            'total',
            'from',
            'to',
            'firstLink',
            'lastLink',
            'links',
        ])->{'links'}->toBeArray();
});