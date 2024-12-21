<?php

use Honed\Table\Actions\BulkAction;

beforeEach(function () {
    $this->action = BulkAction::make('test');
});

it('is not inline by default', function () {
    expect($this->action->isInline())->toBeFalse();
    expect($this->action->isNotInline())->toBeTrue();
});

it('can be set to inline', function () {
    expect($this->action->inline())->toBeInstanceOf(BulkAction::class)
        ->isInline()->toBeTrue();
});

it('can be set to not inline', function () {
    expect($this->action->inline(false))->toBeInstanceOf(BulkAction::class)
        ->isInline()->toBeFalse();
});

it('can be set using setter', function () {
    $this->action->setInline(true);
    expect($this->action->isInline())->toBeTrue();
});

it('does not accept null values', function () {
    $this->action->setInline(null);
    expect($this->action->isInline())->toBeFalse();
});
