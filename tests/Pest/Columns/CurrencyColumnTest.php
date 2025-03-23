<?php

declare(strict_types=1);

use Honed\Table\Columns\CurrencyColumn;

beforeEach(function () {
    $this->column = CurrencyColumn::make('price');
});

it('sets up', function () {
    expect($this->column)
        ->getType()->toBe('currency');
});

it('has cents', function () {
    expect($this->column)
        ->transforms()->toBeFalse()
        ->cents()->toBe($this->column)
        ->transforms()->toBeTrue();
});

it('has currency', function () {
    expect($this->column)
        ->getCurrency()->toBeNull()
        ->currency('USD')->toBe($this->column)
        ->getCurrency()->toBe('USD');
});

it('has locale', function () {
    expect($this->column)
        ->getLocale()->toBeNull()
        ->locale('en_US')->toBe($this->column)
        ->getLocale()->toBe('en_US');
});

it('formats', function () {
    expect($this->column)
        ->formatValue('invalid')->toBeNull()
        ->formatValue(100)->toBe('$100.00');
});
