<?php

declare(strict_types=1);

use Honed\Table\Columns\NumberColumn;

beforeEach(function () {
    $this->param = 'price';
    $this->column = NumberColumn::make($this->param);
});

it('makes', function () {
    expect($this->column)
        ->getName()->toBe($this->param)
        ->getLabel()->toBe(ucfirst($this->param))
        ->getType()->toBe('number');
});

it('has decimals', function () {
    expect($this->column)
        ->getDecimals()->toBeNull()
        ->decimals(2)->toBe($this->column)
        ->getDecimals()->toBe(2);
});

it('has abbreviate', function () {
    expect($this->column)
        ->isAbbreviated()->toBeFalse()
        ->abbreviate()->toBe($this->column)
        ->isAbbreviated()->toBeTrue();
});

it('applies', function () {
    expect($this->column)
        ->fallback(0)->toBe($this->column)
        ->apply(null)->toBe(0)
        ->apply('test')->toBe(0)
        ->apply(1000)->toBe(1000)
        ->abbreviate()->toBe($this->column)
        ->apply(1000)->toBe('1K')
        ->decimals(2)->toBe($this->column)
        ->apply(1.23456789)->toBe('1.23');
});

