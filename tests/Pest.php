<?php

declare(strict_types=1);

use Honed\Table\Table;
use Honed\Table\Tests\TestCase;
use Illuminate\Support\Arr;

uses(TestCase::class)->in(__DIR__);

function getColumn(Table $table, string $name)
{
    return Arr::first(
        $table->getColumns(),
        fn ($column) => $column->getName() === $name
    );
}

expect()->extend('toBeWhere', function (string $column, mixed $value, string $operator = '=', string $boolean = 'and') {
    return $this->toBeArray()
        ->toHaveKeys(['type', 'column', 'value', 'operator', 'boolean'])
        ->{'type'}->toBe('Basic')
        ->{'column'}->toBe($column)
        ->{'value'}->toBe($value)
        ->{'operator'}->toBe($operator)
        ->{'boolean'}->toBe($boolean);
});

expect()->extend('toBeOnlyWhere', function (string $column, mixed $value, string $operator = '=', string $boolean = 'and') {
    return $this->toBeArray()
        ->toHaveCount(1)
        ->{0}->toBeWhere($column, $value, $operator, $boolean);
});

expect()->extend('toBeWhereDate', function (string $column, string $operator, string $value, string $boolean = 'and') {
    return $this->toBeArray()
        ->toHaveKeys(['type', 'column', 'value', 'operator', 'boolean'])
        ->{'type'}->toBe('Date')
        ->{'column'}->toBe($column)
        ->{'value'}->toBe($value)
        ->{'operator'}->toBe($operator)
        ->{'boolean'}->toBe($boolean);
});

expect()->extend('toBeWhereIn', function (string $column, array $values, string $boolean = 'and') {
    return $this->toBeArray()
        ->toHaveKeys(['type', 'column', 'values', 'boolean'])
        ->{'type'}->toBe('In')
        ->{'column'}->toBe($column)
        ->{'values'}->toEqual($values)
        ->{'boolean'}->toBe($boolean);
});

expect()->extend('toBeOnlyWhereIn', function (string $column, array $values, string $boolean = 'and') {
    return $this->toBeArray()
        ->toHaveCount(1)
        ->{0}->toBeWhereIn($column, $values, $boolean);
});

expect()->extend('toBeSearch', function (string $column, string $boolean = 'and') {
    return $this->toBeArray()
        ->toHaveKeys(['type', 'sql', 'boolean'])
        ->{'type'}->toBe('raw')
        ->{'sql'}->toBe(\sprintf('LOWER(%s) LIKE ?', $column))
        ->{'boolean'}->toBe($boolean);
});

expect()->extend('toBeOnlySearch', function (string $column, string $boolean = 'and') {
    return $this->toBeArray()
        ->toHaveCount(1)
        ->{0}->toBeSearch($column, $boolean);
});

expect()->extend('toBeOrder', function (string $column, string $direction = 'asc') {
    return $this->toBeArray()
        ->toHaveKeys(['column', 'direction'])
        ->{'column'}->toBe($column)
        ->{'direction'}->toBe($direction);
});

expect()->extend('toBeOnlyOrder', function (string $column, string $direction = 'asc') {
    return $this->toBeArray()
        ->toHaveCount(1)
        ->{0}->toBeOrder($column, $direction);
});
