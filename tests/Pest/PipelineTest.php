<?php

declare(strict_types=1);

use Honed\Table\Tests\Fixtures\Table as FixtureTable;
use Honed\Table\Tests\Stubs\Status;
use Illuminate\Http\Request;

it('builds', function () {

    foreach (\range(1, 100) as $i) {
        product();
    }
    $request = Request::create('/', 'GET', [
        'name' => 'test',

        'price' => 100,
        'status' => \sprintf('%s,%s', Status::Available->value, Status::Unavailable->value),
        'only' => Status::ComingSoon->value,

        'favourite' => '1',

        'oldest' => '2000-01-01',
        'newest' => '2001-01-01',

        'missing' => 'test',

        FixtureTable::SortsKey => '-price',
        FixtureTable::SearchesKey => 'search+term',
    ]);

    expect(FixtureTable::make()
        ->request($request)
        ->build()
    )->getFor()->getQuery()->scoped(fn ($query) => $query
        ->wheres->scoped(fn ($wheres) => $wheres
            ->toBeArray()
            ->toHaveCount(9)
            ->toEqualCanonicalizing([
                // Search done on name (column) and description (property)
                [
                    'type' => 'raw',
                    'sql' => searchSql('name'),
                    'boolean' => 'or',
                ],
                [
                    'type' => 'raw',
                    'sql' => searchSql('description'),
                    'boolean' => 'and',
                ],
                // Name where filter
                [
                    'type' => 'raw',
                    'sql' => searchSql('name'),
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
                    'type' => 'Basic',
                    'column' => qualifyProduct('status'),
                    'operator' => '=',
                    'value' => Status::ComingSoon->value,
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
                'record' => 'id',
                'delimiter' => FixtureTable::Delimiter,
                'records' => FixtureTable::RecordsKey,
                'sorts' => FixtureTable::SortsKey,
                'searches' => FixtureTable::SearchesKey,
                'columns' => FixtureTable::ColumnsKey,
                'pages' => FixtureTable::PagesKey,
                'endpoint' => FixtureTable::Endpoint,
                'search' => 'search term'
            ])->{'actions'}->scoped(fn ($actions) => $actions
                ->toHaveKeys([ 'hasInline', 'bulk', 'page'])
                ->{'hasInline'}->toBeTrue()
                ->{'bulk'}->toHaveCount(1)
                ->{'page'}->toHaveCount(2)
            )->{'toggleable'}->toBe(FixtureTable::Toggle)
            ->{'sorts'}->toHaveCount(4)
            ->{'filters'}->toHaveCount(7)
            ->{'columns'}->toHaveCount(9)
            ->{'meta'}->toBeEmpty()
        );
});