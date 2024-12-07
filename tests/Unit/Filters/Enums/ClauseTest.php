<?php

use Honed\Table\Filters\Enums\Clause;
use Honed\Table\Filters\Enums\Operator;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->operator = Operator::Equal;
    $this->builder = Product::query();
    $this->attribute = 'name';
    $this->value = 'test';
});

it('has an `is` clause', function () {
    expect(Clause::Is)
        ->statement()->toBe('where')
        ->needsOperator()->toBeTrue()
        ->isMultiple()->toBeFalse()
        ->overrideOperator($this->operator)->toBe($this->operator)
        ->formatValue(1)->toBe(1)
        ->apply($this->builder, $this->attribute, $this->operator, $this->value);
    
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'Basic',
                'column' => $this->attribute,
                'operator' => $this->operator->value,
                'value' => $this->value,
                'boolean' => 'and',
            ],
        ]);
});

it('has an `is not` clause', function () {
    expect(Clause::IsNot)
        ->statement()->toBe('whereNot')
        ->needsOperator()->toBeTrue()
        ->isMultiple()->toBeFalse()
        ->overrideOperator($this->operator)->toBe($this->operator)
        ->formatValue(1)->toBe(1)
        ->apply($this->builder, $this->attribute, $this->operator, $this->value);
    
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'Basic',
                'column' => $this->attribute,
                'operator' => $this->operator->value,
                'value' => $this->value,
                'boolean' => 'and not',
            ],
        ]);
});

it('has an `starts with` clause', function () {
    expect(Clause::StartsWith)
        ->statement()->toBe('where')
        ->needsOperator()->toBeTrue()
        ->isMultiple()->toBeFalse()
        ->overrideOperator($this->operator)->toBe(Operator::Like)
        ->formatValue(1)->toBe('1%')
        ->apply($this->builder, $this->attribute, $this->operator, $this->value);
    
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'Basic',
                'column' => $this->attribute,
                'operator' => Operator::Like->value,
                'value' => $this->value . '%',
                'boolean' => 'and',
            ],
        ]);
});

it('has an `ends with` clause', function () {
    expect(Clause::EndsWith)
        ->statement()->toBe('where')
        ->needsOperator()->toBeTrue()
        ->isMultiple()->toBeFalse()
        ->overrideOperator($this->operator)->toBe(Operator::Like)
        ->formatValue(1)->toBe('%1')
        ->apply($this->builder, $this->attribute, $this->operator, $this->value);
    
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'Basic',
                'column' => $this->attribute,
                'operator' => Operator::Like->value,
                'value' => '%' . $this->value,
                'boolean' => 'and',
            ],
        ]);
});

it('has a `contains` clause', function () {
    expect(Clause::Contains)
        ->statement()->toBe('whereIn')
        ->needsOperator()->toBeFalse()
        ->isMultiple()->toBeTrue()
        ->overrideOperator($this->operator)->toBe($this->operator)
        ->formatValue(1)->toBe([1])
        ->apply($this->builder, $this->attribute, $this->operator, $this->value);
    
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'In',
                'column' => $this->attribute,
                'values' => [$this->value],
                'boolean' => 'and',
            ],
        ]);
});

it('has a `does not contain` clause', function () {
    expect(Clause::DoesNotContain)
        ->statement()->toBe('whereNotIn')
        ->needsOperator()->toBeFalse()
        ->isMultiple()->toBeTrue()
        ->overrideOperator($this->operator)->toBe($this->operator)
        ->formatValue(1)->toBe([1])
        ->apply($this->builder, $this->attribute, $this->operator, $this->value);
    
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'NotIn',
                'column' => $this->attribute,
                'values' => [$this->value],
                'boolean' => 'and',
            ],
        ]);
});


it('has a `json contains` clause', function () {
    expect(Clause::Json)
        ->statement()->toBe('whereJsonContains')
        ->needsOperator()->toBeTrue()
        ->isMultiple()->toBeTrue()
        ->overrideOperator($this->operator)->toBe($this->operator)
        ->formatValue(1)->toBe([1])
        ->apply($this->builder, $this->attribute, $this->operator, $this->value);
    
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'JsonContains',
                'column' => $this->attribute,
                'value' => '=',
                'boolean' => [$this->value],
                'not' => false,
            ],
        ]);
});

it('has a `json does not contain` clause', function () {
    expect(Clause::NotJson)
        ->statement()->toBe('whereJsonDoesntContain')
        ->needsOperator()->toBeTrue()
        ->isMultiple()->toBeTrue()
        ->overrideOperator($this->operator)->toBe($this->operator)
        ->formatValue(1)->toBe([1])
        ->apply($this->builder, $this->attribute, $this->operator, $this->value);
    
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'JsonContains',
                'column' => $this->attribute,
                'value' => '=',
                'boolean' => [$this->value],
                'not' => true,
            ],
        ]);
});

it('has a `json length` clause', function () {
    expect(Clause::JsonLength)
        ->statement()->toBe('whereJsonLength')
        ->needsOperator()->toBeFalse()
        ->isMultiple()->toBeFalse()
        ->overrideOperator($this->operator)->toBe($this->operator)
        ->formatValue(1)->toBe(1)
        ->apply($this->builder, $this->attribute, $this->operator, $this->value);
    
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'JsonLength',
                'column' => $this->attribute,
                'value' => $this->value,
                'operator' => '=',
                'boolean' => 'and',
            ],
        ]);
});

it('has a `json key` clause', function () {
    expect(Clause::JsonKey)
        ->statement()->toBe('whereJsonContainsKey')
        ->needsOperator()->toBeFalse()
        ->isMultiple()->toBeFalse()
        ->overrideOperator($this->operator)->toBe($this->operator)
        ->formatValue(1)->toBe(1)
        ->apply($this->builder, $this->attribute, $this->operator, $this->value);
    
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'JsonContainsKey',
                'column' => $this->attribute,
                'boolean' => $this->value,
                'not' => false,
            ],
        ]);
});

it('has a `json not key` clause', function () {
    expect(Clause::JsonNotKey)
        ->statement()->toBe('whereJsonDoesntContainKey')
        ->needsOperator()->toBeFalse()
        ->isMultiple()->toBeFalse()
        ->overrideOperator($this->operator)->toBe($this->operator)
        ->formatValue(1)->toBe(1)
        ->apply($this->builder, $this->attribute, $this->operator, $this->value);
    
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'JsonContainsKey',
                'column' => $this->attribute,
                'boolean' => $this->value,
                'not' => true,
            ],
        ]);
});

it('has a `json overlaps` clause', function () {
    expect(Clause::JsonOverlaps)
        ->statement()->toBe('whereJsonOverlaps')
        ->needsOperator()->toBeFalse()
        ->isMultiple()->toBeFalse()
        ->overrideOperator($this->operator)->toBe($this->operator)
        ->formatValue(1)->toBe(1)
        ->apply($this->builder, $this->attribute, $this->operator, $this->value);
    
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'JsonOverlaps',
                'column' => $this->attribute,
                'boolean' => 'and',
                'not' => false,
                'value' => $this->value,
            ],
        ]);
});

it('has a `json does not overlap` clause', function () {
    expect(Clause::JsonDoesNotOverlap)
        ->statement()->toBe('whereJsonDoesntOverlap')
        ->needsOperator()->toBeFalse()
        ->isMultiple()->toBeFalse()
        ->overrideOperator($this->operator)->toBe($this->operator)
        ->formatValue(1)->toBe(1)
        ->apply($this->builder, $this->attribute, $this->operator, $this->value);
    
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'JsonOverlaps',
                'column' => $this->attribute,
                'boolean' => 'and',
                'not' => true,
                'value' => $this->value,
            ],
        ]);
});

it('has a `full text` clause', function () {
    expect(Clause::FullText)
        ->statement()->toBe('whereFullText')
        ->needsOperator()->toBeFalse()
        ->isMultiple()->toBeFalse()
        ->overrideOperator($this->operator)->toBe($this->operator)
        ->formatValue(1)->toBe(1)
        ->apply($this->builder, $this->attribute, $this->operator, $this->value);
    
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'Fulltext',
                'boolean' => 'and',
                'value' => $this->value,
                'columns' => [$this->attribute],
                'options' => []

            ],
        ]);
});

it('has a `like` clause', function () {
    expect(Clause::Like)
        ->statement()->toBe('where')
        ->needsOperator()->toBeTrue()
        ->isMultiple()->toBeFalse()
        ->overrideOperator($this->operator)->toBe(Operator::Like)
        ->formatValue(1)->toBe('%1%')
        ->apply($this->builder, $this->attribute, $this->operator, $this->value);
    
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'Basic',
                'column' => $this->attribute,
                'operator' => Operator::Like->value,
                'value' => '%' . $this->value . '%',
                'boolean' => 'and',
            ],
        ]);
});