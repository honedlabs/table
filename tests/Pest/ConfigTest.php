<?php

declare(strict_types=1);

use Honed\Table\Tests\Fixtures\Table as FixtureTable;
use Honed\Table\Table;

beforeEach(function () {
    $this->test = FixtureTable::make();
});

it('has a sorts key', function () {
    $sortsKey = 's';

    // Class-based
    expect($this->test)
        ->getSortsKey()->toBe(FixtureTable::SortsKey)
        ->sortsKey($sortsKey)->toBe($this->test)
        ->getSortsKey()->toBe($sortsKey);

    // Anonymous
    expect(Table::make())
        ->getSortsKey()->toBe(config('table.config.sorts'))
        ->sortsKey($sortsKey)->toBeInstanceOf(Table::class)
        ->getSortsKey()->toBe($sortsKey);
});

it('has a searches key', function () {
    $searchesKey = 's';

    // Class-based
    expect($this->test)
        ->getSearchesKey()->toBe(FixtureTable::SearchesKey)
        ->searchesKey($searchesKey)->toBe($this->test)
        ->getSearchesKey()->toBe($searchesKey);

    // Anonymous
    expect(Table::make())
        ->getSearchesKey()->toBe(config('table.config.searches'))
        ->searchesKey($searchesKey)->toBeInstanceOf(Table::class)
        ->getSearchesKey()->toBe($searchesKey);
});

it('can match', function () {
    $matching = true;

    // Class-based
    expect($this->test)
        ->isMatching()->toBe(config('table.matches'));

    expect($this->test->match($matching))
        ->toBe($this->test)
        ->isMatching()->toBe($matching);

    // Anonymous
    expect(Table::make())
        ->isMatching()->toBe(config('table.matches'));

    expect(Table::make()->match($matching))
        ->toBeInstanceOf(Table::class)
        ->isMatching()->toBe($matching);
});

it('has a delimiter', function () {
    $delimiter = '|';

    // Class-based
    expect($this->test)
        ->getDelimiter()->toBe(FixtureTable::Delimiter)
        ->delimiter($delimiter)->toBe($this->test)
        ->getDelimiter()->toBe($delimiter);

    // Anonymous
    expect(Table::make())
        ->getDelimiter()->toBe(config('table.config.delimiter'))
        ->delimiter($delimiter)->toBeInstanceOf(Table::class)
        ->getDelimiter()->toBe($delimiter);
});

it('has key', function () {
    $key = 'test';

    // Class-based
    expect($this->test)
        ->key($key)->toBe($this->test)
        ->getRecordKey()->toBe($key);

    // Anonymous
    expect(Table::make())
        ->key($key)->toBeInstanceOf(Table::class)
        ->getRecordKey()->toBe($key);
});

it('errors if no key is set', function () {
    expect(fn () => Table::make()->getRecordKey())
        ->toThrow(\RuntimeException::class);
});

it('has endpoint', function () {
    $endpoint = '/other';

    // Class-based
    expect($this->test)
        ->getEndpoint()->toBe(FixtureTable::Endpoint)
        ->endpoint($endpoint)->toBe($this->test)
        ->getEndpoint()->toBe($endpoint);

    // Anonymous
    expect(Table::make())
        ->getEndpoint()->toBe(config('table.endpoint'))
        ->endpoint($endpoint)->toBeInstanceOf(Table::class)
        ->getEndpoint()->toBe($endpoint);
});