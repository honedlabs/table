<?php

use Honed\Table\Filters\SetFilter;

beforeEach(function () {
    $this->filter = SetFilter::make('name');
});

it('is not strict by default', function () {
    expect($this->filter->onlyStrictValues())->toBeFalse();
    expect($this->filter->allowsAllValues())->toBeTrue();
});

it('does not accept null values', function () {
    $this->filter->setStrict(null);
    expect($this->filter->onlyStrictValues())->toBeFalse();
    expect($this->filter->allowsAllValues())->toBeTrue();
});

it('can be set to allow all values', function () {
    expect($this->filter->strict(true))->toBeInstanceOf(SetFilter::class)
        ->onlyStrictValues()->toBeTrue()
        ->allowsAllValues()->toBeFalse();
});

it('can be set using setter', function () {
    $this->filter->setStrict(true);
    expect($this->filter->onlyStrictValues())->toBeTrue();
    expect($this->filter->allowsAllValues())->toBeFalse();
});

