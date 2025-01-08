<?php

declare(strict_types=1);

use Honed\Table\Columns\BaseColumn;
use Honed\Table\Tests\Stubs\Product;

class BaseColumnTest extends BaseColumn {}

beforeEach(function () {
    $this->name = 'name';
    $this->test = new BaseColumnTest($this->name);
});

it('can be made', function () {
    expect(BaseColumnTest::make($this->name))
        ->toBeInstanceOf(BaseColumnTest::class)
        ->getName()->toBe($this->name)
        ->getLabel()->toBe('Name');
});

it('has array representation', function () {
    expect($this->test->toArray())
        ->toBeArray()
        ->toHaveKeys([
            'name',
            'label',
            'type',
            'breakpoint',
            'hidden',
            'sr_only',
            'toggleable',
            'active',
            'sortable',
            'direction',
            'meta',
        ]);
});

it('can be applied to a record', function () {
    $product = product();
    expect($this->test->transformer(fn (Product $product) => $product->name)
        ->apply($product))->toBe($product->name);
});

it('formats with placeholder', function () {
    expect($this->test->placeholder('test'))->formatValue(null)->toBe('test');
});