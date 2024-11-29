<?php

use Honed\Table\Url\Url;

beforeEach(function () {
    $this->url = Url::make();
});

it('is not signed by default', function () {
    expect($this->url->isSigned())->toBeFalse();
    expect($this->url->isNotSigned())->toBeTrue();
});

it('can be set to signed', function () {
    expect($this->url->signed())->toBeInstanceOf(Url::class)
        ->isSigned()->toBeTrue();
});

it('can be set to not signed', function () {
    expect($this->url->signed(false))->toBeInstanceOf(Url::class)
        ->isSigned()->toBeFalse();
});

it('can be set using setter', function () {
    $this->url->setSigned(true);
    expect($this->url->isSigned())->toBeTrue();
});

it('does not accept null values', function () {
    $this->url->setSigned(null);
    expect($this->url->isSigned())->toBeFalse();
});