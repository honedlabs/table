<?php

use Honed\Core\Concerns\Evaluable;
use Honed\Table\Confirm\Concerns\HasCancel;
use Honed\Table\Confirm\Confirm;
use Honed\Table\Tests\Stubs\Product;

class HasCancelTest
{
    use HasCancel;
    use Evaluable;
}

beforeEach(function () {
    $this->confirm = Confirm::make();
});

beforeEach(function () {
    $this->test = new HasCancelTest;
});

it('has no cancel by default', function () {
    expect($this->test)
        ->getCancel()->toBeNull()
        ->hasCancel()->toBeFalse();
});

it('sets cancel', function () {
    $this->test->setCancel('Cancel');
    expect($this->test)
        ->getCancel()->toBe('Cancel')
        ->hasCancel()->toBeTrue();
});

it('rejects null values', function () {
    $this->test->setCancel('Cancel');
    $this->test->setCancel(null);
    expect($this->test)
        ->getCancel()->toBe('Cancel')
        ->hasCancel()->toBeTrue();
});

it('chains cancel', function () {
    expect($this->test->cancel('Cancel'))->toBeInstanceOf(HasCancelTest::class)
        ->getCancel()->toBe('Cancel')
        ->hasCancel()->toBeTrue();
});

it('resolves cancel', function () {
    $product = product();

    expect($this->test->cancel(fn (Product $product) => $product->name))
        ->toBeInstanceOf(HasCancelTest::class)
        ->resolveCancel(['product' => $product])->toBe($product->name)
        ->getCancel()->toBe($product->name);
});
