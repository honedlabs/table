<?php

use Honed\Table\Actions\InlineAction;
use Honed\Table\Url\Url;

beforeEach(function () {
    $this->urlable = InlineAction::make('test');
});

it('has no url by default', function () {
    expect($this->urlable->isUrlable())->toBeFalse();
    expect($this->urlable->isNotUrlable())->toBeTrue();
});

it('can set a url using a string', function () {
    expect($this->urlable->url('https://example.com'))->toBeInstanceOf(InlineAction::class)
        ->isUrlable()->toBeTrue();

    expect($this->urlable->getUrl()->getUrl())->toBe('https://example.com');
});

it('can set a url using a URL instance', function () {
    expect($this->urlable->url(Url::make('product.show')))
        ->toBeInstanceOf(InlineAction::class)
        ->isUrlable()->toBeTrue();
    
    expect($this->urlable->getUrl())
        ->getUrl()->toBe('product.show')
        ->isNamed()->toBeTrue();
});

it('can be set using key value pairs', function () {
    expect($this->urlable->url([
        'url' => 'https://example.com',
        'signed' => true,
        'duration' => 10,
    ]))->toBeInstanceOf(InlineAction::class)
        ->isUrlable()->toBeTrue();

    expect($this->urlable->getUrl())
        ->getUrl()->toBe('https://example.com')
        ->isSigned()->toBeTrue()
        ->getDuration()->toBe(10);
});

it('can be set using a closure', function () {
    expect($this->urlable->url(fn (Url $url) => $url->url('https://example.com')->download()))
        ->toBeInstanceOf(InlineAction::class)
        ->isUrlable()->toBeTrue();
    
    expect($this->urlable->getUrl())
        ->getUrl()->toBe('https://example.com')
        ->isDownload()->toBeTrue();
});

it('proxies case when it is a uri', function () {
    expect($this->urlable->url('/products'))
        ->toBeInstanceOf(InlineAction::class)
        ->isUrlable()->toBeTrue();

    expect($this->urlable->getUrl())
        ->getUrl()->toBe('/products')
        ->isNamed()->toBeFalse();
});

it('has to alias', function () {
    expect($this->urlable->to('product.show'))->toBeInstanceOf(InlineAction::class)
        ->isUrlable()->toBeTrue();

    expect($this->urlable->getUrl()->getUrl())->toBe('product.show');
});

it('can make a new url instance', function () {
    expect($this->urlable->makeUrl())->toBeInstanceOf(Url::class);
    expect($this->urlable->isUrlable())->toBeTrue();
});