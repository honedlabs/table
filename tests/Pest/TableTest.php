<?php

declare(strict_types=1);

use Honed\Table\Table;
use Honed\Table\Columns\KeyColumn;
use Honed\Table\Tests\Fixtures\Table as FixturesTable;

beforeEach(function () {
    $this->table = Table::make();
});

it('has key', function () {
    expect($this->table)
        ->withColumns(KeyColumn::make('id'))
        ->getKey()->toBe('id')
        ->key('test')->toBe($this->table)
        ->getKey()->toBe('test');
});

it('requires key', function () {
    $this->table->getKey();
})->throws(\RuntimeException::class);

it('has endpoint', function () {
    expect($this->table)
        ->getEndpoint()->toBe(config('table.endpoint'))
        ->endpoint('/other')->toBe($this->table)
        ->getEndpoint()->toBe('/other')
        ->getDefaultEndpoint()->toBe(config('table.endpoint'));
});

it('serializes', function () {
    expect($this->table)
        ->isSerialized()->toBe(config('table.serialize'))
        ->serializes(true)->toBe($this->table)
        ->isSerialized()->toBe(true)
        ->isSerializedByDefault()->toBe(config('table.serialize'));
});

it('has records', function () {
    expect($this->table)
        ->getRecords()->toBeEmpty();

    $this->table->setRecords([
        [
            'id' => 1,
        ]
    ]);

    expect($this->table)
        ->getRecords()->not->toBeEmpty();
});

it('has pagination data', function () {
    expect($this->table)
        ->getPaginationData()->toBeEmpty();

    $this->table->setPaginationData([
        'empty' => true,
    ]);

    expect($this->table)
        ->getPaginationData()->not->toBeEmpty();

});

it('overrides refine fallbacks', function () {
    expect($this->table)
        ->getDelimiter()->toBe(config('table.delimiter'))
        ->getSearchKey()->toBe(config('table.search_key'))
        ->getMatchKey()->toBe(config('table.match_key'))
        ->isMatching()->toBe(config('table.match'));
});

it('is url routable', function () {
    // $key = $this->table->encode($this->table);

    // expect($this->table)
    //     ->getRouteKey()->toBe($key);

    expect($this->table)
        ->getRouteKeyName()->toBe('table');

    expect($this->table)
        ->resolveRouteBinding($this->table->getRouteKey())
        ->toBeNull();

    $table = FixturesTable::make();

    expect($table)
        ->resolveRouteBinding($table->getRouteKey())
        ->toBeInstanceOf(FixturesTable::class);

    expect($table)
        ->resolveChildRouteBinding(null, $table->getRouteKey())
        ->toBeInstanceOf(FixturesTable::class);
});

it('calls macro', function () {
    Table::macro('test', function () {
        return $this->getEndpoint();
    });

    expect($this->table)
        ->test()->toBe(config('table.endpoint'));
});
