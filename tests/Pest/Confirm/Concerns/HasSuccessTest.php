<?php

use Honed\Table\Confirm\Confirm;

beforeEach(function () {
    $this->confirm = Confirm::make();
});

it('does not have a success message by default', function () {
    expect($this->confirm->getSuccess())->toBeNull();
    expect($this->confirm->missingSuccess())->toBeTrue();
    expect($this->confirm->hasSuccess())->toBeFalse();
});

it('can set a success message', function () {
    expect($this->confirm->success('Updated'))->toBeInstanceOf(Confirm::class)
        ->getSuccess()->toBe('Updated');
});

it('can be set using setter', function () {
    $this->confirm->setSuccess('Update');
    expect($this->confirm->getSuccess())->toBe('Update');
});

it('does not accept null values', function () {
    $this->confirm->setSuccess(null);
    expect($this->confirm->getSuccess())->toBeNull();
});
