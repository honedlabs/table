<?php

use Honed\Table\Actions\InlineAction;

beforeEach(function () {
    $this->action = InlineAction::make('test');
});

it('is not bulk by default', function () {
    expect($this->action->isBulk())->toBeFalse();
    expect($this->action->isNotBulk())->toBeTrue();
});

it('can be set to bulk', function () {
    expect($this->action->bulk())->toBeInstanceOf(InlineAction::class)
        ->isBulk()->toBeTrue();
});

it('can be set to not bulk', function () {
    expect($this->action->bulk(false))->toBeInstanceOf(InlineAction::class)
        ->isBulk()->toBeFalse();
});

it('can be set using setter', function () {
    $this->action->setBulk(true);
    expect($this->action->isBulk())->toBeTrue();
});

it('does not accept null values', function () {
    $this->action->setBulk(null);
    expect($this->action->isBulk())->toBeFalse();
});
