<?php

use Honed\Table\Filters\SetFilter;

beforeEach(function () {
    $this->filter = SetFilter::make('name');
});

it('is not multiple by default', function () {
    expect($this->filter->isMultiple())->toBeFalse();
    expect($this->filter->isSingle())->toBeTrue();
});

it('can be set to multiple', function () {
    expect($this->filter->multiple())->toBeInstanceOf(SetFilter::class)
        ->isMultiple()->toBeTrue();
});

it('does not accept null values', function () {
    $this->filter->setMultiple(null);
    expect($this->filter->isMultiple())->toBeFalse();
    expect($this->filter->isSingle())->toBeTrue();
});

it('can be set using setter', function () {
    $this->filter->setMultiple(true);
    expect($this->filter->isMultiple())->toBeTrue();
    expect($this->filter->isSingle())->toBeFalse();
});

