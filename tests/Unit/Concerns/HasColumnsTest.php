<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;
use Honed\Table\Table;

beforeEach(function () {
    $this->table = Table::make();
});

it('is empty by default', function () {
    expect($this->table)
        ->hasColumns()->toBeFalse()
        ->getColumns()->toBeEmpty();
});

it('adds columns', function () {
    expect($this->table)
        ->columns([Column::make('id')])->toBe($this->table)
        ->columns([Column::make('public_id')])->toBe($this->table)
        ->hasColumns()->toBeTrue()
        ->getColumns()->toHaveCount(2);
});

it('adds columns variadically', function () {
    expect($this->table)
        ->columns(Column::make('id'), Column::make('public_id'))->toBe($this->table)
        ->hasColumns()->toBeTrue()
        ->getColumns()->toHaveCount(2);
});

it('adds columns collection', function () {
    expect($this->table)
        ->columns(collect([Column::make('id'), Column::make('public_id')]))->toBe($this->table)
        ->hasColumns()->toBeTrue()
        ->getColumns()->toHaveCount(2);
});

it('retrieves with authorization', function () {
    expect($this->table)
        ->columns([
            Column::make('id')->allow(false), 
            Column::make('public_id'),
        ])->toBe($this->table)
        ->hasColumns()->toBeTrue()
        ->getColumns()->toHaveCount(1);
});

it('caches columns', function () {
    expect($this->table)
        ->cacheColumns([Column::make('id')->allow(false), Column::make('public_id')])->toBe($this->table)
        ->getCachedColumns()->toHaveCount(2);
    
    $this->table->flushCachedColumns();

    expect($this->table)
        ->getCachedColumns()->toBeEmpty();
});

it('can be without columns', function () {
    expect($this->table)
        ->isWithoutColumns()->toBeFalse()
        ->columns(Column::make('public_id'))->toBe($this->table)
        ->hasColumns()->toBeTrue()
        ->getColumns()->toHaveCount(1)
        ->withoutColumns()->toBe($this->table)
        ->hasColumns()->toBeFalse()
        ->getColumns()->toBeEmpty();
});

it('has array representation', function () {
    expect($this->table)
        ->columnsToArray()->toBeEmpty();
});