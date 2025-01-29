<?php

use Honed\Core\Concerns\Evaluable;
use Honed\Table\Columns\Concerns\HasTooltip;
use Honed\Table\Tests\Stubs\Product;

class HasTooltipTest
{
    use Evaluable;
    use HasTooltip;
}

beforeEach(function () {
    $this->test = new HasTooltipTest;
});

it('has no tooltip by default', function () {
    expect($this->test)
        ->getTooltip()->toBeNull()
        ->hasTooltip()->toBeFalse();
});

it('sets tooltip', function () {
    $this->test->setTooltip('Tooltip');
    expect($this->test)
        ->getTooltip()->toBe('Tooltip')
        ->hasTooltip()->toBeTrue();
});

it('rejects null values', function () {
    $this->test->setTooltip('Tooltip');
    $this->test->setTooltip(null);
    expect($this->test)
        ->getTooltip()->toBe('Tooltip')
        ->hasTooltip()->toBeTrue();
});

it('chains tooltip', function () {
    expect($this->test->tooltip('Tooltip'))->toBeInstanceOf(HasTooltipTest::class)
        ->getTooltip()->toBe('Tooltip')
        ->hasTooltip()->toBeTrue();
});

it('resolves tooltip', function () {
    $product = product();
    expect($this->test->tooltip(fn (Product $product) => $product->name))
        ->toBeInstanceOf(HasTooltipTest::class)
        ->resolveTooltip(['product' => $product])->toBe($product->name)
        ->getTooltip()->toBe($product->name);
});
