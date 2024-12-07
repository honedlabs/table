<?php

use Honed\Table\Filters\SetFilter;

beforeEach(function () {
    $this->filter = SetFilter::make('name');
});

it('is not strict by default', function () {
    expect($this->filter->allowsOnlyStrictValues())->toBeFalse();
    expect($this->filter->allowsAllValues())->toBeTrue();
});

it('does not accept null values', function () {
    $this->filter->setOnlyStrictValues(null);
    expect($this->filter->allowsOnlyStrictValues())->toBeFalse();
    expect($this->filter->allowsAllValues())->toBeTrue();
});

it('can be set to allow all values', function () {
    expect($this->filter->onlyStrictValues(true))->toBeInstanceOf(SetFilter::class)
        ->allowsOnlyStrictValues()->toBeTrue()
        ->allowsAllValues()->toBeFalse();
});

it('can be set using setter', function () {
    $this->filter->setOnlyStrictValues(true);
    expect($this->filter->allowsOnlyStrictValues())->toBeTrue();
    expect($this->filter->allowsAllValues())->toBeFalse();
});

