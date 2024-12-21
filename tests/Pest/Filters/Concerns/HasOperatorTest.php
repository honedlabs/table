<?php

use Honed\Table\Filters\Enums\Operator;
use Honed\Table\Filters\Filter;

beforeEach(function () {
    $this->filter = Filter::make('name');
});

it('can set a operator', function () {
    expect($this->filter->operator(Operator::Equal))->toBeInstanceOf(Filter::class)
        ->getOperator()->toBe(Operator::Equal);
});

it('can be set using setter', function () {
    $this->filter->setOperator(Operator::LessThan);
    expect($this->filter->getOperator())->toBe(Operator::LessThan);
});

it('does not accept null values', function () {
    expect($this->filter->getOperator())->toBe(Operator::Equal);
    $this->filter->setOperator(null);
    expect($this->filter->getOperator())->toBe(Operator::Equal);
});

it('checks if it has a operator', function () {
    expect($this->filter->hasOperator())->toBeTrue();
    expect($this->filter->missingOperator())->toBeFalse();
});

it('has shorthand for `gt`', function () {
    expect($this->filter->gt())->toBeInstanceOf(Filter::class)
        ->getOperator()->toBe(Operator::GreaterThan);
});

it('has shorthand for `gte`', function () {
    expect($this->filter->gte())->toBeInstanceOf(Filter::class)
        ->getOperator()->toBe(Operator::GreaterThanOrEqual);
});

it('has shorthand for `lt`', function () {
    expect($this->filter->lt())->toBeInstanceOf(Filter::class)
        ->getOperator()->toBe(Operator::LessThan);
});

it('has shorthand for `lte`', function () {
    expect($this->filter->lte())->toBeInstanceOf(Filter::class)
        ->getOperator()->toBe(Operator::LessThanOrEqual);
});

it('has shorthand for `eq`', function () {
    expect($this->filter->eq())->toBeInstanceOf(Filter::class)
        ->getOperator()->toBe(Operator::Equal);
});

it('has shorthand for `neq`', function () {
    expect($this->filter->neq())->toBeInstanceOf(Filter::class)
        ->getOperator()->toBe(Operator::NotEqual);
});

it('has alias for `eq` as `equals`', function () {
    expect($this->filter->equals())->toBeInstanceOf(Filter::class)
        ->getOperator()->toBe(Operator::Equal);
});

it('has alias for `eq` as `equal`', function () {
    expect($this->filter->equal())->toBeInstanceOf(Filter::class)
        ->getOperator()->toBe(Operator::Equal);
});

it('has alias for `neq` as `notEqual`', function () {
    expect($this->filter->notEqual())->toBeInstanceOf(Filter::class)
        ->getOperator()->toBe(Operator::NotEqual);
});

it('has alias for `gt` as `greaterThan`', function () {
    expect($this->filter->greaterThan())->toBeInstanceOf(Filter::class)
        ->getOperator()->toBe(Operator::GreaterThan);
});

it('has alias for `gte` as `greaterThanOrEqual`', function () {
    expect($this->filter->greaterThanOrEqual())->toBeInstanceOf(Filter::class)
        ->getOperator()->toBe(Operator::GreaterThanOrEqual);
});

it('has alias for `lt` as `lessThan`', function () {
    expect($this->filter->lessThan())->toBeInstanceOf(Filter::class)
        ->getOperator()->toBe(Operator::LessThan);
});

it('has alias for `lte` as `lessThanOrEqual`', function () {
    expect($this->filter->lessThanOrEqual())->toBeInstanceOf(Filter::class)
        ->getOperator()->toBe(Operator::LessThanOrEqual);
});

it('has shorthand for `like` operator as `search`', function () {
    expect($this->filter->search())->toBeInstanceOf(Filter::class)
        ->getOperator()->toBe(Operator::Like);
});
