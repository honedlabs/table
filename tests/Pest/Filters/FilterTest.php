<?php

use Honed\Table\Filters\Enums\Clause;
use Honed\Table\Filters\Enums\Operator;
use Honed\Table\Filters\Filter;
use Honed\Table\Tests\Stubs\Product;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

beforeEach(function () {
    $this->name = 'email';
    $this->value = 'test@example.com';
    $this->filter = Filter::make($this->name);
    $this->builder = Product::query();
    Request::swap(Request::create('/', HttpFoundationRequest::METHOD_GET, [$this->name => $this->value]));
});

it('has a default clause and operator', function () {
    expect($this->filter)
        ->getType()->toBe('filter')
        ->getClause()->toBe(Clause::Is)
        ->getOperator()->toBe(Operator::Equal);
});

it('can be applied', function () {
    $this->filter->apply($this->builder);

    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->{0}->toEqual([
            'type' => 'Basic',
            'column' => $this->name,
            'value' => $this->value,
            'operator' => Operator::Equal->value,
            'boolean' => 'and',
        ]);

    expect($this->filter)
        ->isActive()->toBeTrue()
        ->getValue()->toBe($this->value);
});

it('does not apply when parameter name is not present', function () {
    $request = Request::create('/', HttpFoundationRequest::METHOD_GET, ['fake' => $this->value]);

    $this->filter->apply($this->builder, $request);
    expect($this->builder->getQuery()->wheres)
        ->toBeEmpty();

    expect($this->filter)
        ->isActive()->toBeFalse()
        ->getValue()->toBeNull();
});

it('handles different clauses and operators', function () {
    $this->filter->clause(Clause::IsNot)->operator($o = Operator::GreaterThan);
    $this->filter->apply($this->builder);
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->{0}->toEqual([
            'type' => 'Basic',
            'column' => $this->name,
            'value' => $this->value,
            'operator' => $o->value,
            'boolean' => 'and not',
        ]);
});
