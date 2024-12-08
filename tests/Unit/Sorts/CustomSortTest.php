<?php

use Honed\Table\Sorts\CustomSort;
use Honed\Table\Tests\Stubs\Product;
use Illuminate\Support\Facades\Request;

beforeEach(function () {
    $this->sortName = 'sort';
    $this->orderName = 'order';
    $this->sortKey = 'created_at';
    $this->order = 'asc';
    $this->builder = Product::query();
    $this->sort = CustomSort::make($this->sortKey);
    Request::swap(Request::create('/', 'GET', [$this->sortName => $this->sortKey, $this->orderName => $this->order]));
});

it('has a type', function () {
    expect(CustomSort::make('name'))->toBeInstanceOf(CustomSort::class)
        ->getType()->toBe('sort:custom');
});

it('does not execute if the query is missing', function () {
    $this->sort->apply($this->builder, $this->sortName, $this->orderName);
    expect($this->builder->getQuery()->orders)->toBeEmpty();
});

it('uses a custom query', function () {
    expect($this->sort->query(fn ($query, $direction) => $query->orderBy('other_column', $direction)))
        ->toBeInstanceOf(CustomSort::class);

    $this->sort->apply($this->builder, $this->sortName, $this->orderName);
    expect($this->builder->getQuery()->orders)
        ->toHaveCount(1)
        ->toEqual([
            [
                'column' => 'other_column',
                'direction' => CustomSort::Ascending,
            ],
        ]);

    expect($this->sort)
        ->isActive()->toBeTrue()
        ->getActiveDirection()->toBe(CustomSort::Ascending);
});
