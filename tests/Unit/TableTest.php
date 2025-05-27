<?php

declare(strict_types=1);

use Honed\Table\Table;
use Honed\Table\Columns\KeyColumn;
use Honed\Table\EmptyState;
use Honed\Table\Exceptions\KeyNotFoundException;
use Honed\Table\Tests\Stubs\Product;
use Honed\Table\Tests\Stubs\ProductTable;

beforeEach(function () {
    $this->table = Table::make();
});

it('has key', function () {
    expect($this->table)
        ->columns(KeyColumn::make('id'))
        ->getKey()->toBe('id')
        ->key('test')->toBe($this->table)
        ->getKey()->toBe('test');
});

it('requires key', function () {
    $this->table->getKey();
})->throws(KeyNotFoundException::class);

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

it('has empty state', function () {
    expect($this->table)
        ->getEmptyState()->toBeInstanceOf(EmptyState::class)
        ->emptyState('string')->toBe($this->table)
        ->getEmptyState()->scoped(fn ($state) => $state
            ->getMessage()->toBe('string')
        )->emptyState(fn ($state) => $state->message('closure'))->toBe($this->table)
        ->getEmptyState()->scoped(fn ($state) => $state
            ->getMessage()->toBe('closure')
        )->emptyState(EmptyState::make('title'))->toBe($this->table)
        ->getEmptyState()->scoped(fn ($state) => $state
            ->getTitle()->toBe('title')
        );
});

it('overrides refine fallbacks', function () {
    expect($this->table)
        ->getDelimiter()->toBe(config('table.delimiter'))
        ->getSearchKey()->toBe(config('table.search_key'))
        ->getMatchKey()->toBe(config('table.match_key'))
        ->isMatching()->toBe(config('table.match'));
});

it('is url routable', function () {
    expect($this->table)
        ->getRouteKeyName()->toBe('table');

    expect($this->table)
        ->resolveRouteBinding($this->table->getRouteKey())
        ->toBeNull();

    $table = ProductTable::make();

    expect($table)
        ->resolveRouteBinding($table->getRouteKey())
        ->toBeInstanceOf(ProductTable::class);

    expect($table)
        ->resolveChildRouteBinding(null, $table->getRouteKey())
        ->toBeInstanceOf(ProductTable::class);
});

it('resolves table', function () {
    ProductTable::guessTableNamesUsing(function ($class) {
        return $class.'Table';
    });

    expect(ProductTable::resolveTableName(Product::class))
        ->toBe('Honed\\Table\\Tests\\Stubs\\ProductTable');

    expect(ProductTable::tableForModel(Product::class))
        ->toBeInstanceOf(ProductTable::class);

    ProductTable::flushState();
});

it('uses namespace', function () {
    ProductTable::useNamespace('');

    expect(ProductTable::resolveTableName(Product::class))
        ->toBe('Honed\\Table\\Tests\\Stubs\\ProductTable');

    ProductTable::flushState();
});

it('calls macro', function () {
    Table::macro('test', function () {
        return $this->getEndpoint();
    });

    expect($this->table)
        ->test()->toBe(config('table.endpoint'));
});
