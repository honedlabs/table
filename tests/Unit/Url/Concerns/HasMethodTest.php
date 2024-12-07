<?php

use Honed\Table\Url\Url;
use Symfony\Component\HttpFoundation\Request;

beforeEach(function () {
    $this->url = Url::make();
});

it('uses the get method by default', function () {
    expect($this->url->getMethod())->toBe('get');
});

it('can set a method', function () {
    expect($this->url->method(Request::METHOD_POST))->toBeInstanceOf(Url::class)
        ->getMethod()->toBe('post');
});

it('can be set using setter', function () {
    $this->url->setMethod(Request::METHOD_POST);
    expect($this->url->getMethod())->toBe('post');
});

it('does not accept null values', function () {
    $this->url->setMethod(null);
    expect($this->url->getMethod())->toBe('get');
});

it('has shorthand for get method', function () {
    expect($this->url->get())->toBeInstanceOf(Url::class)
        ->getMethod()->toBe('get');
});

it('has shorthand for post method', function () {
    expect($this->url->post())->toBeInstanceOf(Url::class)
        ->getMethod()->toBe('post');
});

it('has shorthand for put method', function () {
    expect($this->url->put())->toBeInstanceOf(Url::class)
        ->getMethod()->toBe('put');
});

it('has shorthand for patch method', function () {
    expect($this->url->patch())->toBeInstanceOf(Url::class)
        ->getMethod()->toBe('patch');
});

it('has shorthand for delete method', function () {
    expect($this->url->delete())->toBeInstanceOf(Url::class)
        ->getMethod()->toBe('delete');
});

it('does not accept invalid methods', function () {
    expect(fn () => $this->url->method('invalid'))->toThrow(\InvalidArgumentException::class);
});
