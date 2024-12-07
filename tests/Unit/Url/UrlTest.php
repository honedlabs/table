<?php

use Honed\Table\Url\Url;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->url = Url::make();
});

it('has an array form', function () {
    expect($this->url->toArray())->toEqual([
        'url' => null,
        'method' => 'get',
    ]);
});

it('can be made', function () {
    expect(Url::make('https://example.com'))->toBeInstanceOf(Url::class)
        ->toArray()->toEqual([
            'url' => 'https://example.com',
            'method' => 'get',
        ]);
});

it('has shorthand method for named routes', function () {
    expect($this->url->to('product.show'))->toBeInstanceOf(Url::class)
        ->hasUrl()->toBeTrue()
        ->isNamed()->toBeTrue();
});

it('has shorthand method for signed routes', function () {
    expect($this->url->signedRoute('product.show', 100))->toBeInstanceOf(Url::class)
        ->hasUrl()->toBeTrue()
        ->isTemporary()->toBeTrue()
        ->isSigned()->toBeTrue()
        ->isNamed()->toBeTrue();
});

it('resolves uris', function () {
    expect($this->url->url(fn ($record) => sprintf('%s.com', $record)))->toBeInstanceOf(Url::class)
        ->hasUrl()->toBeTrue()
        ->getResolvedUrl(['record' => 'google'])->toBe('google.com');
});

it('resolves named routes', function () {
    expect($this->url->to('product.show'))->toBeInstanceOf(Url::class)
        ->hasUrl()->toBeTrue()
        ->isNamed()->toBeTrue()
        ->getResolvedUrl([Product::find(1)])->toBe(config('app.url').'/product/1');
});

it('resolves signed temporary routes', function () {
    expect($this->url->signedRoute(fn (Product $product) => '/product/'.$product->id, now()->addMinutes(10)))->toBeInstanceOf(Url::class)
        ->hasUrl()->toBeTrue()
        ->isNamed()->toBeFalse()
        ->getResolvedUrl([], [Product::class => Product::find(1)])->toBe('/product/1');
});
