<?php

use Honed\Core\Options\Option;
use Honed\Table\Filters\Enums\Clause;
use Honed\Table\Filters\Enums\Operator;
use Honed\Table\Filters\SetFilter;
use Honed\Table\Tests\Stubs\Product;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

beforeEach(function () {
    $this->name = 'name';
    $this->value = 'test';
    $this->filter = SetFilter::make($this->name);
    $this->builder = Product::query();
    Request::swap(Request::create('/', HttpFoundationRequest::METHOD_GET, [
        $this->name => $this->value,
    ]));
});

it('has defaults', function () {
    expect($this->filter)
        ->getType()->toBe('filter:set')
        ->getClause()->toBe(Clause::Is)
        ->getOperator()->toBe(Operator::Equal)
        ->hasOptions()->toBeFalse()
        ->isMultiple()->toBeFalse()
        ->isStrict()->toBeFalse();
});

it('can be applied', function () {
    $this->filter->apply($this->builder);

    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->{0}->toEqual([
            'type' => 'Basic',
            'column' => $this->name,
            'value' => $this->value,
            'operator' => '=',
            'boolean' => 'and',
        ]);

    expect($this->filter)
        ->isActive()->toBeTrue()
        ->getValue()->toBe($this->value);
});

it('is not applied when parameter name is not present', function () {
    $request = Request::create('/', HttpFoundationRequest::METHOD_GET, ['fake' => $this->value]);

    $this->filter->apply($this->builder, $request);

    expect($this->builder->getQuery()->wheres)
        ->toBeEmpty();

    expect($this->filter)
        ->isActive()->toBeFalse()
        ->getValue()->toBeNull();
});

it('has options', function () {
    $this->filter
        ->options([
            Option::make('test3', 'Test 3'),
            Option::make('test2', 'Test 2'),
            Option::make($this->value, 'Test'),
        ])
        ->apply($this->builder);

    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->{0}->toEqual([
            'type' => 'Basic',
            'column' => $this->name,
            'value' => $this->value,
            'operator' => '=',
            'boolean' => 'and',
        ]);

    expect($this->filter)
        ->hasOptions()->toBeTrue()
        ->collectOptions()->scoped(fn ($options) => $options
        ->toHaveCount(3)
        ->first(fn (Option $option) => $option->isActive())
        ->getValue()->toBe($this->value)
        );
});

it('accepts multiple values', function () {
    $value2 = 'test2';
    $request = Request::create('/', HttpFoundationRequest::METHOD_GET, [$this->name => \sprintf('%s,%s', $this->value, $value2)]);

    $this->filter->options([
        Option::make('test3', 'Test 3'),
        Option::make('test2', 'Test 2'),
        Option::make($this->value, 'Test'),
    ])
        ->multiple()
        ->apply($this->builder, $request);

    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->{0}->toEqual([
            'type' => 'In',
            'column' => $this->name,
            'values' => [$this->value, $value2],
            'boolean' => 'and',
        ]);

    expect($this->filter)
        ->hasOptions()->toBeTrue()
        ->collectOptions()->scoped(fn ($options) => $options
        ->toHaveCount(3)
        ->sequence(
            fn ($option) => $option->isActive()->toBeFalse(),
            fn ($option) => $option->isActive()->toBeTrue(),
            fn ($option) => $option->isActive()->toBeTrue(),
        )
        );
});

it('has array representation', function () {
    expect($this->filter->toArray())
        ->toBeArray()
        ->toHaveKeys([
            'name',
            'label',
            'type',
            'active',
            'value',
            'meta',
            'options',
            'multiple',
        ]);
});
