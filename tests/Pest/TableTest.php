<?php

declare(strict_types=1);

use Honed\Table\Table;
use Honed\Table\Columns\KeyColumn;

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
        ->fallbackEndpoint()->toBe(config('table.endpoint'));
});

it('has attributes', function () {
    expect($this->table)
        ->hasAttributes()->toBe(config('table.attributes'))
        ->attributes(true)->toBe($this->table)
        ->hasAttributes()->toBe(true)
        ->fallbackAttributes()->toBe(config('table.attributes'));
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