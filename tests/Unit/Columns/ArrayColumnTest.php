<?php

declare(strict_types=1);

use Honed\Table\Columns\ArrayColumn;
use Illuminate\Support\Arr;

beforeEach(function () {
    $this->column = ArrayColumn::make('categories');
});

it('sets up', function () {
    expect($this->column)
        ->isActive()->toBeTrue()
        ->getType()->toBe('array');
});

it('has pluck', function () {
    expect($this->column)
        ->getPluck()->toBeNull()
        ->pluck('name')->toBe($this->column)
        ->getPluck()->toBe('name');
});

it('has glue', function () {
    expect($this->column)
        ->getGlue()->toBeNull()
        ->glue(', ')->toBe($this->column)
        ->getGlue()->toBe(', ');
});

describe('applies', function () {
    beforeEach(function () {
        $product = product();

        $product->categories()->attach(category('A'));
        $product->categories()->attach(category('B'));

        $product->load('categories');

        $this->value = Arr::get($product, 'categories');
    });

    it('falls back when not iterable', function () {
        expect($this->column->name('name')->fallback('-')
            ->apply(null))->toBe('-');
    });

    it('formats', function () {
        expect($this->column->apply($this->value))
            ->toBeArray()
            ->toHaveCount(2);
    });

    it('plucks', function () {
        expect($this->column->pluck('name')->apply($this->value))
            ->toBeArray()
            ->toHaveCount(2)
            ->toEqual(['A', 'B']);
    });

    it('glues', function () {
        expect($this->column->pluck('name')->glue(', ')->apply($this->value))
            ->toBeString()
            ->toEqual('A, B');
    });
});