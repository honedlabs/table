<?php

use Honed\Table\Url\Concerns\HasMethod;
use Honed\Table\Url\Url;
use Symfony\Component\HttpFoundation\Request;

class HasMethodTest
{
    use HasMethod;
}

beforeEach(function () {
    $this->test = new HasMethodTest();
});

it('is get method by default', function () {
    expect($this->test->getMethod())->toBe(Request::METHOD_GET);
});

it('sets method', function () {
    $this->test->setMethod(Request::METHOD_POST);
    expect($this->test->getMethod())->toBe(Request::METHOD_POST);
});

it('chains method', function () {
    expect($this->test->method(Request::METHOD_POST))->toBeInstanceOf(HasMethodTest::class)
        ->getMethod()->toBe(Request::METHOD_POST);
});

it('rejects null values', function () {
    $this->test->method(Request::METHOD_POST);
    $this->test->setMethod(null);
    expect($this->test->getMethod())->toBe(Request::METHOD_POST);
});

it('has shorthand `asGet`', function () {
    expect($this->test->asGet())->toBeInstanceOf(HasMethodTest::class)
        ->getMethod()->toBe(Request::METHOD_GET);
});

it('has shorthand `asPost`', function () {
    expect($this->test->asPost())->toBeInstanceOf(HasMethodTest::class)
        ->getMethod()->toBe(Request::METHOD_POST);
});

it('has shorthand `asPut`', function () {
    expect($this->test->asPut())->toBeInstanceOf(HasMethodTest::class)
        ->getMethod()->toBe(Request::METHOD_PUT);
});

it('has shorthand `asPatch`', function () {
    expect($this->test->asPatch())->toBeInstanceOf(HasMethodTest::class)
        ->getMethod()->toBe(Request::METHOD_PATCH);
});

it('has shorthand `asDelete`', function () {
    expect($this->test->asDelete())->toBeInstanceOf(HasMethodTest::class)
        ->getMethod()->toBe(Request::METHOD_DELETE);
});

it('does not accept invalid methods', function () {
    expect(fn () => $this->test->method('invalid'))->toThrow(\InvalidArgumentException::class);
});
