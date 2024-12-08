<?php

use Honed\Table\Tests\Stubs\Product;
use Honed\Table\Filters\CustomFilter;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Builder;

beforeEach(function () {
    $request = Request::create('/', 'GET', ['name' => 'something']);
    Request::swap($request);
    $this->filter = CustomFilter::make('name');
    $this->builder = Product::query();
});

it('has no query by default', function () {
    expect($this->filter)
        ->getType()->toBe('filter:custom')
        ->hasQuery()->toBeFalse();
});

it('can apply the filter to a query', function () {
    $this->filter->query(fn (Builder $builder) => $builder->where('name', 'test'));
    $this->filter->apply($this->builder);
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'Basic',
                'column' => 'name',
                'operator' => '=',
                'value' => 'test',
                'boolean' => 'and',
            ],
        ]);

    expect($this->filter)
        ->isActive()->toBeTrue()
        ->getValue()->toBe('something');
});

it('does not apply the filter if the request does not have a matching value', function () {
    Request::swap(Request::create('/', 'GET', ['email' => 'test@example.com']));

    $this->filter->query(fn (Builder $builder) => $builder->where('name', 'test'));

    $this->filter->apply($this->builder);
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(0);

    expect($this->filter)
        ->isActive()->toBeFalse()
        ->getValue()->toBeNull();
});

it('does not apply the filter if there is no query', function () {
    $this->filter->apply($this->builder);
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(0);
});

