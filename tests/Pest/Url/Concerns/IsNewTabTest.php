<?php

use Honed\Table\Url\Url;

beforeEach(function () {
    $this->url = Url::make();
});

it('is not newTab by default', function () {
    expect($this->url->isNewTab())->toBeFalse();
    expect($this->url->isNotNewTab())->toBeTrue();
});

it('can be set to newTab', function () {
    expect($this->url->newTab())->toBeInstanceOf(Url::class)
        ->isNewTab()->toBeTrue();
});

it('can be set to not newTab', function () {
    expect($this->url->newTab(false))->toBeInstanceOf(Url::class)
        ->isNewTab()->toBeFalse();
});

it('can be set using setter', function () {
    $this->url->setNewTab(true);
    expect($this->url->isNewTab())->toBeTrue();
});

it('does not accept null values', function () {
    $this->url->setNewTab(null);
    expect($this->url->isNewTab())->toBeFalse();
});
