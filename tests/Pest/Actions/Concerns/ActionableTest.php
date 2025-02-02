<?php

use Honed\Table\Actions\Concerns\Actionable;
use Honed\Table\Tests\Stubs\Product;

class ActionableTest
{
    use Actionable;
}

class InvokableTest
{
    public function __invoke()
    {
        return true;
    }
}

beforeEach(function () {
    $this->test = new ActionableTest();
    $this->fn = fn (Product $product) => $product->touch();
});

it('has no action by default', function () {
    expect($this->test)
        ->hasAction()->toBeFalse()
        ->getAction()->toBeNull();
});

it('sets action', function () {
    $this->test->setAction($this->fn);
    expect($this->test)
        ->hasAction()->toBeTrue()
        ->getAction()->toBeInstanceOf(\Closure::class);
});

it('chains action', function () {
    expect($this->test->action($this->fn))->toBeInstanceOf(ActionableTest::class)
        ->hasAction()->toBeTrue()
        ->getAction()->toBeInstanceOf(\Closure::class);
});

it('rejects null values', function () {
    $this->test->setAction($this->fn);
    $this->test->setAction(null);

    expect($this->test)
        ->hasAction()->toBeTrue()
        ->getAction()->toBeInstanceOf(\Closure::class);
});

it('accepts invokable class actions', function () {
    expect($this->test->action(InvokableTest::class))->toBeInstanceOf(ActionableTest::class)
        ->hasAction()->toBeTrue()
        ->getAction()->toBeInstanceOf(\Closure::class);
});