<?php

use Honed\Table\Filters\Enums\Clause;
use Honed\Table\Filters\Enums\Operator;
use Honed\Table\Filters\Filter;
use Illuminate\Support\Facades\Request;
use Workbench\App\Models\Product;

beforeEach(function () {
    $request = Request::create('/', 'GET', ['name' => 'test']);
    Request::swap($request);
    $this->filter = Filter::make('name');
    $this->builder = Product::query();
});

it('has a default clause and operator', function () {
    expect($this->filter)
        ->getClause()->toBe(Clause::Is)
        ->getOperator()->toBe(Operator::Equal);
});

it('can apply the filter to a query', function () {
    $this->filter->apply($this->builder);
    expect($this->builder->toRawSql())->toBe('select * from "products" where "name" = \'test\'');
    expect($this->filter)
        ->isActive()->toBeTrue()
        ->getValue()->toBe('test');
});

it('does not apply the filter if the request does not have a matching value', function () {
    Request::swap(Request::create('/', 'GET', ['email' => 'test@example.com']));

    $this->filter->apply($this->builder);
    expect($this->builder->toRawSql())->toBe('select * from "products"');
    expect($this->filter)
        ->isActive()->toBeFalse()
        ->getValue()->toBeNull();
});

it('handles different clauses and operators', function () {
    $this->filter->clause(Clause::IsNot)->operator(Operator::Equal);
    $this->filter->apply($this->builder);
    expect($this->builder->toRawSql())->toBe('select * from "products" where not "name" = \'test\'');
});
