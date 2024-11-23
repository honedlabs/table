<?php

use Honed\Table\Actions\BulkAction;

beforeEach(function () {
    $this->action = BulkAction::make('test');
});

it('is not deselectOnEnd by default', function () {
    expect($this->action->deselectsOnEnd())->toBeFalse();
    expect($this->action->doesNotDeselectOnEnd())->toBeTrue();
});

it('can be set to deselectOnEnd', function () {
    expect($this->action->deselectOnEnd())->toBeInstanceOf(BulkAction::class)
        ->deselectsOnEnd()->toBeTrue();
});

it('can be set to not deselectOnEnd', function () {
    expect($this->action->deselectOnEnd(false))->toBeInstanceOf(BulkAction::class)
        ->deselectsOnEnd()->toBeFalse();
});

it('can be set using setter', function () {
    $this->action->setDeselectOnEnd(true);
    expect($this->action->deselectsOnEnd())->toBeTrue();
});

it('does not accept null values', function () {
    $this->action->setDeselectOnEnd(null);
    expect($this->action->deselectsOnEnd())->toBeFalse();
});