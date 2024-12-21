<?php

use Honed\Table\Filters\Enums\DateClause;
use Honed\Table\Filters\Enums\Operator;
use Honed\Table\Tests\Stubs\Product;

beforeEach(function () {
    $this->operator = Operator::Equal;
    $this->builder = Product::query();
    $this->attribute = 'created_at';
    $this->value = '2000-01-01';
});

it('has a `date` clause', function () {
    expect(DateClause::Date)
        ->statement()->toBe('whereDate')
        ->formatValue($this->value)->toBe($this->value)
        ->apply($this->builder, $this->attribute, $this->operator, $this->value);

    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'Date',
                'column' => $this->attribute,
                'operator' => $this->operator->value,
                'value' => $this->value,
                'boolean' => 'and',
            ],
        ]);
});

it('has a `day` clause', function () {
    expect(DateClause::Day)
        ->statement()->toBe('whereDay')
        ->formatValue($this->value)->toBe('1')
        ->apply($this->builder, $this->attribute, $this->operator, $this->value);

    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'Day',
                'column' => $this->attribute,
                'operator' => $this->operator->value,
                'value' => '01',
                'boolean' => 'and',
            ],
        ]);
});

it('has a `month` clause', function () {
    expect(DateClause::Month)
        ->statement()->toBe('whereMonth')
        ->formatValue($this->value)->toBe('1')
        ->apply($this->builder, $this->attribute, $this->operator, $this->value);

    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'Month',
                'column' => $this->attribute,
                'operator' => $this->operator->value,
                'value' => '01',
                'boolean' => 'and',
            ],
        ]);
});

it('has a `year` clause', function () {
    expect(DateClause::Year)
        ->statement()->toBe('whereYear')
        ->formatValue($this->value)->toBe('2000')
        ->apply($this->builder, $this->attribute, $this->operator, $this->value);

    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'Year',
                'column' => $this->attribute,
                'operator' => $this->operator->value,
                'value' => '2000',
                'boolean' => 'and',
            ],
        ]);
});

it('has a `time` clause', function () {
    expect(DateClause::Time)
        ->statement()->toBe('whereTime')
        ->formatValue($this->value)->toBe('00:00:00')
        ->apply($this->builder, $this->attribute, $this->operator, $this->value);

    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'Time',
                'column' => $this->attribute,
                'operator' => $this->operator->value,
                'value' => '00:00:00',
                'boolean' => 'and',
            ],
        ]);
});

it('short circuits if the value is not a valid date', function () {
    DateClause::Date->apply($this->builder, $this->attribute, $this->operator, 'not a date');
    expect($this->builder->getQuery()->wheres)->toHaveCount(0);
});
