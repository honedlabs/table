<?php

declare(strict_types=1);

use Honed\Table\PageOption;
use Honed\Table\Table;
use Honed\Table\Tests\Stubs\Product;

beforeEach(function () {
    $this->table = Table::make();
});

it('has paginate', function () {
    expect($this->table)
        ->getPaginate()->toBe(Table::LENGTH_AWARE)
        ->paginate(false)->toBe($this->table)
        ->getPaginate()->toBe(Table::COLLECTION)
        ->paginate(true)->toBe($this->table)
        ->getPaginate()->toBe(Table::LENGTH_AWARE)
        ->cursorPaginate()->toBe($this->table)
        ->getPaginate()->toBe(Table::CURSOR)
        ->simplePaginate()->toBe($this->table)
        ->getPaginate()->toBe(Table::SIMPLE)
        ->lengthAwarePaginate()->toBe($this->table)
        ->getPaginate()->toBe(Table::LENGTH_AWARE)
        ->dontPaginate()->toBe($this->table)
        ->getPaginate()->toBe(Table::COLLECTION);
});

it('has per page', function () {
    expect($this->table)
        ->getPerPage()->toBe(Table::PER_PAGE)
        ->perPage(5)->toBe($this->table)
        ->getPerPage()->toBe(5);
});

it('has default per page', function () {
    expect($this->table)
        ->getDefaultPerPage()->toBe(Table::PER_PAGE)
        ->defaultPerPage(5)->toBe($this->table)
        ->getDefaultPerPage()->toBe(5);
});

it('has page key', function () {
    expect($this->table)
        ->getPageKey()->toBe(Table::PAGE_KEY)
        ->pageKey('test')->toBe($this->table)
        ->getPageKey()->toBe('test');
});

it('has records key', function () {
    expect($this->table)
        ->getRecordKey()->toBe(Table::RECORD_KEY)
        ->recordKey('test')->toBe($this->table)
        ->getRecordKey()->toBe('test');
});

it('has window', function () {
    expect($this->table)
        ->getWindow()->toBe(Table::WINDOW)
        ->window(5)->toBe($this->table)
        ->getWindow()->toBe(5);
});

it('has page options', function () {
    expect($this->table)
        ->getPageOptions()->toBeEmpty();

    $this->table->createPageOptions([5, 10, 20], 20);

    expect($this->table->getPageOptions())
        ->toBeArray()
        ->toHaveCount(3)
        ->{0}
        ->scoped(fn ($perPage) => $perPage
            ->toBeInstanceOf(PageOption::class)
            ->getValue()->toBe(5)
            ->isActive()->toBeFalse()
        )
        ->{1}
        ->scoped(fn ($perPage) => $perPage
            ->toBeInstanceOf(PageOption::class)
            ->getValue()->toBe(10)
            ->isActive()->toBeFalse()
        )
        ->{2}
        ->scoped(fn ($perPage) => $perPage
            ->toBeInstanceOf(PageOption::class)
            ->getValue()->toBe(20)
            ->isActive()->toBeTrue()
        );

    expect($this->table)
        ->pageOptionsToArray()->toEqual([
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

// it('paginates collection', function () {
//     $paginated = Product::query()->get();

//     expect($this->table->collectionPaginator($paginated))
//         ->toBeArray()
//         ->toHaveKeys(['empty']);
// });

// it('paginates cursor', function () {
//     $paginated = Product::query()->cursorPaginate();

//     expect($this->table->cursorPaginator($paginated))
//         ->toBeArray()
//         ->toHaveKeys([
//             'empty',
//             'prevLink',
//             'nextLink',
//             'perPage',
//         ]);
// });

// it('paginates simple', function () {
//     $paginated = Product::query()->simplePaginate();

//     expect($this->table->simplePaginator($paginated))
//         ->toBeArray()
//         ->toHaveKeys([
//             'empty',
//             'prevLink',
//             'nextLink',
//             'perPage',
//             'currentPage',
//         ]);
// });

// it('paginates length-aware', function () {
//     $paginated = Product::query()->paginate();

//     expect($this->table->lengthAwarePaginator($paginated))
//         ->toBeArray()
//         ->toHaveKeys([
//             'empty',
//             'prevLink',
//             'nextLink',
//             'perPage',
//             'currentPage',
//             'total',
//             'from',
//             'to',
//             'firstLink',
//             'lastLink',
//             'links',
//         ])->{'links'}->toBeArray();
// });
