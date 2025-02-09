<?php

declare(strict_types=1);

use Honed\Table\Concerns\Selectable;
use Honed\Table\Tests\Stubs\Product;

class SelectableTest
{
    use Selectable;
}

class SelectableMethodTest extends SelectableTest
{
    public function selectable(Product $product): bool
    {
        return $product->name === 'name';
    }
}

beforeEach(function () {
    $this->test = new SelectableTest;
    $this->method = new SelectableMethodTest;
    $this->fn = fn (Product $product) => $product->name === 'name';
});

it('is selectable by default', function () {
    expect($this->test)
        ->hasSelector()->toBeFalse()
        ->isSelectable(product('other'))->toBeTrue();
});

it('sets selectable', function () {
    $this->test->setSelectable($this->fn);

    expect($this->test)
        ->hasSelector()->toBeTrue()
        ->isSelectable(product('name'))->toBeTrue();
});

it('retrieves selector', function () {
    expect($this->method->getSelector())
        ->toBeArray()
        ->{0}->toBeInstanceOf(SelectableMethodTest::class)
        ->{1}->toBeString('selectable');
});

it('applies selector', function () {
    expect($this->method)
        ->hasSelector()->toBeTrue()
        ->isSelectable(product('name'))->toBeTrue()
        ->isSelectable(product('other'))->toBeFalse();
});
