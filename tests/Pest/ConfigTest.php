<?php

declare(strict_types=1);

use Honed\Refine\Refine;
use Honed\Refine\Tests\Stubs\Product;
use Honed\Table\Tests\Fixtures\Table;

beforeEach(function () {
    $this->test = Table::make();
});

it('has a sorts key', function () {
    expect($this->test)
        ->getSortsKey()->toBe(config('table.config.sorts'))
        ->sortsKey('test')->toBe($this->test)
        ->getSortsKey()->toBe('test');
});

it('has a searches key', function () {
    expect($this->test)
        ->getSearchesKey()->toBe(config('table.config.searches'))
        ->searchesKey('test')->toBe($this->test)
        ->getSearchesKey()->toBe('test');
});

it('can match', function () {
    expect($this->test)
        ->canMatch()->toBe(config('table.matches'));

    expect($this->test->match())->toBe($this->test)
        ->canMatch()->toBeTrue();
});

it('has a delimiter', function () {
    expect($this->test)
        ->getDelimiter()->toBe(config('table.config.delimiter'))
        ->delimiter('|')->toBe($this->test)
        ->getDelimiter()->toBe('|');
});