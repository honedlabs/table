<?php

use Honed\Table\Confirm\Confirm;

beforeEach(function () {
    $this->confirm = Confirm::make();
});

it('does not have a intent type by default', function () {
    expect($this->confirm->getIntent())->toBeNull();
    expect($this->confirm->missingIntent())->toBeTrue();
    expect($this->confirm->hasIntent())->toBeFalse();
});

it('can set a intent type', function () {
    expect($this->confirm->intent('Updated'))->toBeInstanceOf(Confirm::class)
        ->getIntent()->toBe('Updated');
});

it('can be set using setter', function () {
    $this->confirm->setIntent('Update');
    expect($this->confirm->getIntent())->toBe('Update');
});

it('does not accept null values', function () {
    $this->confirm->setIntent(null);
    expect($this->confirm->getIntent())->toBeNull();
});

it('can be set as constructive', function () {
    expect($this->confirm->constructive())->toBeInstanceOf(Confirm::class)
        ->getIntent()->toBe(Confirm::Constructive);
});

it('can be set as destructive', function () {
    expect($this->confirm->destructive())->toBeInstanceOf(Confirm::class)
        ->getIntent()->toBe(Confirm::Destructive);
});

it('can be set as informative', function () {
    expect($this->confirm->informative())->toBeInstanceOf(Confirm::class)
        ->getIntent()->toBe(Confirm::Informative);
});
