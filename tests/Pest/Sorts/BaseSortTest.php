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

it('can be instantiated', function () {
    expect(new Sort('updated_at'))->toBeInstanceOf(Sort::class)
        ->getAttribute()->toBe('updated_at')
        ->getLabel()->toBe('Updated at');
});

it('can be made', function () {
    expect(Sort::make('updated_at', 'Most recent'))->toBeInstanceOf(Sort::class)
        ->getAttribute()->toBe('updated_at')
        ->getLabel()->toBe('Most recent');
});

it('retrieves the value from the request', function () {
    expect($this->sort->getValueFromRequest($this->sortName, $this->orderName))->toEqual([
        $this->sortKey,
        $this->order,
    ]);
});

it('determines if the filter should be applied', function () {
    expect($this->sort->isSorting($this->sortKey, $this->order))->toBeTrue();
    expect($this->sort->isSorting('other', $this->order))->toBeFalse();
});

it('differentiates between dot notation attributes', function () {
    expect(Sort::make('user.created_at', 'User created at'))
        ->getAttribute()->toBe('user.created_at')
        ->getAlias()->toBeNull()
        ->getValueFromRequest($this->sortName, $this->orderName)->toEqual([
            $this->sortKey, // resolves as just created_at
            $this->order,
        ]);
});

it('has an array form', function () {
    // The active status is only applied when the sort itself is applied
    expect($this->sort->toArray())->toEqual([
        'name' => $this->sortKey,
        'label' => 'Created at',
        'type' => 'sort',
        'isActive' => false,
        'meta' => [],
        'direction' => null, // has a direction field as it is agnostic
    ]);
});

it('can apply the filter to a query', function () {
    $this->sort->apply($this->builder, $this->sortName, $this->orderName);
    expect($this->builder->getQuery()->orders)
        ->toHaveCount(1)
        ->toEqual([
            [
                'column' => $this->sortKey,
                'direction' => Sort::Ascending,
            ],
        ]);
    expect($this->sort)
        ->isActive()->toBeTrue()
        ->getActiveDirection()->toBe(Sort::Ascending);
});

it('can extract a direction from the sort name', function () {
    Request::swap(Request::create('/', 'GET', [$this->sortName => '-'.$this->sortKey]));
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
        ->getActiveDirection()->toBe(Sort::Descending);
});
