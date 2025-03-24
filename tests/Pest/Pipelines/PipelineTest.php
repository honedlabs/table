<?php

declare(strict_types=1);

use Honed\Table\Tests\Fixtures\Table as FixtureTable;
use Honed\Table\Tests\Stubs\Status;
use Illuminate\Http\Request;

beforeEach(function () {
    foreach (\range(1, 100) as $i) {
        product();
    }

    $this->request = Request::create('/', 'GET', [
        'name' => 'test',

        'price' => 100,
        'status' => \sprintf('%s,%s', Status::Available->value, Status::Unavailable->value),
        'only' => Status::ComingSoon->value,

        'favourite' => '1',

        'oldest' => '2000-01-01',
        'newest' => '2001-01-01',

        'missing' => 'test',

        config('table.sort_key') => '-price',
        config('table.search_key') => 'search+term',
        config('table.column_key') => 'id,name,price,status,best_seller,created_at',
        config('table.record_key') => 25,
    ]);
});

it('builds class', function () {

    $request = Request::create('/', 'GET', [
        'name' => 'test',

        'price' => 100,
        'status' => \sprintf('%s,%s', Status::Available->value, Status::Unavailable->value),
        'only' => Status::ComingSoon->value,

        'favourite' => '1',

        'oldest' => '2000-01-01',
        'newest' => '2001-01-01',

        'missing' => 'test',

        config('table.sort_key') => '-price',
        config('table.search_key') => 'search+term',
        config('table.column_key') => 'id,name,price,status,best_seller,created_at',
        config('table.record_key') => 25,
    ]);

    expect(FixtureTable::make()
        ->request($request)
        ->build()
    )->getBuilder()->getQuery()->scoped(fn ($query) => $query
        ->wheres->scoped(fn ($wheres) => $wheres
            ->toBeArray()
            ->toHaveCount(9)
            ->toEqualCanonicalizing([
                // Search done on name (column) and description (property)
                [
                    'type' => 'raw',
                    'sql' => searchSql('products.name'),
                    'boolean' => 'or',
                ],
                [
                    'type' => 'raw',
                    'sql' => searchSql('products.description'),
                    'boolean' => 'and',
                ],
                // Name where filter
                [
                    'type' => 'raw',
                    'sql' => searchSql('products.name'),
                    'boolean' => 'and',
                ],
                // Price set filter
                [
                    'type' => 'Basic',
                    'column' => qualifyProduct('price'),
                    'operator' => '<=',
                    'value' => 100,
                    'boolean' => 'and',
                ],
                // Status set filter
                [
                    'type' => 'In',
                    'column' => qualifyProduct('status'),
                    'values' => [Status::Available->value, Status::Unavailable->value],
                    'boolean' => 'and',
                ],
                // Only set filter
                [
                    'type' => 'In',
                    'column' => qualifyProduct('status'),
                    'values' => [Status::ComingSoon->value],
                    'boolean' => 'and',
                ],
                // Favourite filter
                [
                    'type' => 'Basic',
                    'column' => qualifyProduct('best_seller'),
                    'operator' => '=',
                    'value' => true,
                    'boolean' => 'and',
                ],
                // Oldest date filter
                [
                    'type' => 'Date',
                    'column' => qualifyProduct('created_at'),
                    'operator' => '>=',
                    'value' => '2000-01-01',
                    'boolean' => 'and',
                ],
                // Newest date filter
                [
                    'type' => 'Date',
                    'column' => qualifyProduct('created_at'),
                    'operator' => '<=',
                    'value' => '2001-01-01',
                    'boolean' => 'and',
                ],
            ])
        )->orders->scoped(fn ($orders) => $orders
            ->toBeArray()
            ->toHaveCount(1)
            ->{0}->toEqual([
                'column' => qualifyProduct('price'),
                'direction' => 'desc',
            ])
        )
        )->toArray()->scoped(fn ($array) => $array
            ->{'config'}->toEqual([
                'key' => 'id',
                'delimiter' => config('table.delimiter'),
                'record' => config('table.record_key'),
                'sort' => config('table.sort_key'),
                'search' => config('table.search_key'),
                'column' => config('table.column_key'),
                'page' => config('table.page_key'),
                'endpoint' => config('table.endpoint'),
                'term' => 'search term',
                'match' => 'match',
            ])->{'actions'}->scoped(fn ($actions) => $actions
                ->toHaveKeys([ 'hasInline', 'bulk', 'page'])
                ->{'hasInline'}->toBeTrue()
                ->{'bulk'}->toHaveCount(1)
                ->{'page'}->toHaveCount(2)
            )->{'toggleable'}->toBeTrue()
            ->{'sorts'}->toHaveCount(4)
            ->{'filters'}->toHaveCount(8)
            ->{'columns'}->toHaveCount(9)
            ->{'meta'}->toBeEmpty()
        );
});