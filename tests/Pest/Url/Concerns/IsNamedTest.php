<?php

use Honed\Table\Url\Url;

beforeEach(function () {
    $this->url = Url::make();
});

it('is not named by default', function () {
    expect($this->url->isNamed())->toBeFalse();
    expect($this->url->isNotNamed())->toBeTrue();
});

it('can be set to named', function () {
    expect($this->url->Named())->toBeInstanceOf(Url::class)
        ->isNamed()->toBeTrue();
});

it('can be set to not named', function () {
    expect($this->url->Named(false))->toBeInstanceOf(Url::class)
        ->isNamed()->toBeFalse();
});

it('can be set using setter', function () {
    $this->url->setNamed(true);
    expect($this->url->isNamed())->toBeTrue();
});

it('does not accept null values', function () {
    $this->url->setNamed(null);
    expect($this->url->isNamed())->toBeFalse();
});
