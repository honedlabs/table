<?php

declare(strict_types=1);

use Honed\Table\Table;
use Honed\Table\Tests\Fixtures\Table as FixtureTable;
use Honed\Table\Tests\Stubs\Product;
use Illuminate\Database\Eloquent\Builder;

beforeEach(function () {
    $this->test = FixtureTable::make();
});

it('has no modifier by default', function () {
    expect($this->test->buildTable())
        ->hasModifier()->toBeFalse()
        ->getModifier()->toBeNull()
        ->getBuilder()->getQuery()->wheres->toBeEmpty();
});

it('can apply a modifier', function () {
    $fn = fn ($product) => $product->where('best_seller', true);

    expect($this->test)
        ->modifier($fn)
        ->hasModifier()->toBeTrue()
        ->getModifier()->toBe($fn)
        ->buildTable()->getBuilder()->getQuery()->wheres->scoped(fn ($wheres) => $wheres
        ->toBeArray()
        ->toHaveCount(1)
        ->{0}->toEqual([
            'type' => 'Basic',
            'column' => 'best_seller',
            'operator' => '=',
            'value' => true,
            'boolean' => 'and',
        ])
        );
});

it('can apply a modifier anonymously', function () {
    $fn = fn ($product) => $product->where('best_seller', true);

    expect(Table::make()->builder(Product::query())->modifier($fn)->buildTable())
        ->hasModifier()->toBeTrue()
        ->getModifier()->toBe($fn)
        ->buildTable()->getBuilder()->getQuery()->wheres->scoped(fn ($wheres) => $wheres
        ->toBeArray()
        ->toHaveCount(1)
        ->{0}->toEqual([
            'type' => 'Basic',
            'column' => 'best_seller',
            'operator' => '=',
            'value' => true,
            'boolean' => 'and',
        ])
        );
});

it('has a resource', function () {
    expect($this->test)
        ->getResource()->toBeInstanceOf(Builder::class)
        ->getBuilder()->toBeInstanceOf(Builder::class);
});

it('guesses a resource', function () {
    expect(FixtureTable::make()->guessResource())
        ->toBe('\\App\\Models\\');
});
