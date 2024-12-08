<?php

use Honed\Table\Filters\Enums\DateClause;
use Honed\Table\Filters\Enums\Operator;
use Honed\Table\Filters\DateFilter;
use Illuminate\Support\Facades\Request;
use Honed\Table\Tests\Stubs\Product;

beforeEach(function () {
    $request = Request::create('/', 'GET', ['created_at' => '2000-01-01']);
    Request::swap($request);
    $this->filter = DateFilter::make('created_at');
    $this->builder = Product::query();
});

it('has a default clause and operator', function () {
    expect($this->filter)
        ->getType()->toBe('filter:date')
        ->getClause()->toBe(DateClause::Date)
        ->getOperator()->toBe(Operator::Equal);
});

it('can apply the filter to a query', function () {
    $this->filter->apply($this->builder);
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'Date',
                'column' => 'created_at',
                'operator' => '=',
                'value' => '2000-01-01',
                'boolean' => 'and',
            ],
        ]);
    expect($this->filter->getValue())->toBe('2000-01-01');
});

it('does not apply the filter if the request does not have a matching value', function () {
    Request::swap(Request::create('/', 'GET', ['updated_at' => '2000-01-01']));

    $this->filter->apply($this->builder);
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(0);
    expect($this->filter)
        ->isActive()->toBeFalse()
        ->getValue()->toBeNull();
});

it('handles different clauses and operators', function () {
    $this->filter->clause(DateClause::Day)->operator(Operator::NotEqual);
    $this->filter->apply($this->builder);
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'Day',
                'column' => 'created_at',
                'operator' => '!=',
                'value' => '01',
                'boolean' => 'and',
            ],
        ]);
});
