<?php

use Honed\Core\Concerns\Evaluable;
use Honed\Table\Confirm\Concerns\HasSuccess;
use Honed\Table\Confirm\Confirm;
use Honed\Table\Tests\Stubs\Product;

class HasSuccessTest
{
    use HasSuccess;
    use Evaluable;
}

beforeEach(function () {
    $this->confirm = Confirm::make();
});

beforeEach(function () {
    $this->test = new HasSuccessTest;
});

it('has no success by default', function () {
    expect($this->test)
        ->getSuccess()->toBeNull()
        ->hasSuccess()->toBeFalse();
});

it('sets success', function () {
    $this->test->setSuccess('Success');
    expect($this->test)
        ->getSuccess()->toBe('Success')
        ->hasSuccess()->toBeTrue();
});

it('rejects null values', function () {
    $this->test->setSuccess('Success');
    $this->test->setSuccess(null);
    expect($this->test)
        ->getSuccess()->toBe('Success')
        ->hasSuccess()->toBeTrue();
});

it('chains success', function () {
    expect($this->test->success('Success'))->toBeInstanceOf(HasSuccessTest::class)
        ->getSuccess()->toBe('Success')
        ->hasSuccess()->toBeTrue();
});

it('resolves success', function () {
    $product = product();

    expect($this->test->success(fn (Product $product) => $product->name))
        ->toBeInstanceOf(HasSuccessTest::class)
        ->resolveSuccess(['product' => $product])->toBe($product->name)
        ->getSuccess()->toBe($product->name);
});
