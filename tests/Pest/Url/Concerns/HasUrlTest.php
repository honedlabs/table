<?php

use Honed\Table\Url\Url;

beforeEach(function () {
    $this->url = Url::make();
});

it('does not have a url by default', function () {
    expect($this->url->getUrl())->toBeNull();
});

it('can set an url', function () {
    expect($this->url->url('https://example.com'))->toBeInstanceOf(Url::class)
        ->getUrl()->toBe('https://example.com');
});

it('can be set using setter', function () {
    $this->url->setUrl('https://example.com');
    expect($this->url->getUrl())->toBe('https://example.com');
});

it('does not accept null values', function () {
    $this->url->setUrl(null);
    expect($this->url->getUrl())->toBeNull();
});

it('checks if it has a url', function () {
    expect($this->url->hasUrl())->toBeFalse();
    $this->url->setUrl('https://example.com');
    expect($this->url->hasUrl())->toBeTrue();
});
