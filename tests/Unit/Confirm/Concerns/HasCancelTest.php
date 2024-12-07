<?php

use Honed\Table\Confirm\Confirm;

beforeEach(function () {
    $this->confirm = Confirm::make();
});

it('does not have a cancel message by default', function () {
    expect($this->confirm->getCancel())->toBeNull();
    expect($this->confirm->missingCancel())->toBeTrue();
    expect($this->confirm->hasCancel())->toBeFalse();
});

it('can set a cancel message', function () {
    expect($this->confirm->cancel('Updated'))->toBeInstanceOf(Confirm::class)
        ->getCancel()->toBe('Updated');
});

it('can be set using setter', function () {
    $this->confirm->setCancel('Update');
    expect($this->confirm->getCancel())->toBe('Update');
});

it('does not accept null values', function () {
    $this->confirm->setCancel(null);
    expect($this->confirm->getCancel())->toBeNull();
});
