<?php

use Honed\Table\Sorts\Sort;
use Honed\Table\Tests\Stubs\Product;
use Illuminate\Support\Facades\Request;

beforeEach(function () {
    $this->sortName = 'sort';
    $this->orderName = 'order';
    $this->sortKey = 'created_at';
    $this->order = 'asc';
    $this->builder = Product::query();
    Sort::useDescending();
    $this->sort = Sort::make($this->sortKey);
    Request::swap(Request::create('/', 'GET', [$this->sortName => $this->sortKey, $this->orderName => $this->order]));
});

it('has a type', function () {
    expect(Sort::make('name'))->toBeInstanceOf(Sort::class)
        ->getType()->toBe('sort');
});

it('uses the default direction if none is specified', function () {
    Request::swap(Request::create('/', 'GET', [$this->sortName => $this->sortKey]));
    $this->sort->apply($this->builder, $this->sortName, $this->orderName);
    expect($this->builder->getQuery()->orders)
        ->toHaveCount(1)
        ->toEqual([
            [
                'column' => $this->sortKey,
                'direction' => Sort::Descending,
            ],
        ]);

    expect($this->sort)
        ->isActive()->toBeTrue()
        ->getActiveDirection()->toBeNull();
});
