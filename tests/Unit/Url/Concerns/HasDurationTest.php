<?php

use Honed\Table\Url\Url;

beforeEach(function () {
    $this->url = Url::make();
});

it('does not have a duration by default', function () {
    expect($this->url->getDuration())->toBe(0);
});

it('can set a duration', function () {
    expect($this->url->duration(1))->toBeInstanceOf(Url::class)
        ->getDuration()->toBe(1);
});

it('can be set using setter', function () {
    $this->url->setDuration(1);
    expect($this->url->getDuration())->toBe(1);
});

it('does not accept null values', function () {
    $this->url->setDuration(null);
    expect($this->url->getDuration())->toBe(0);
});

it('checks if it is temporary', function () {
    expect($this->url->isTemporary())->toBeFalse();
    expect($this->url->isNotTemporary())->toBeTrue();
    $this->url->setDuration(1);
    expect($this->url->isTemporary())->toBeTrue();
    expect($this->url->isNotTemporary())->toBeFalse();
});