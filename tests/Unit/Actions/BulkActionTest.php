<?php

use Honed\Table\Actions\BulkAction;

beforeEach(function () {
    $this->action = BulkAction::make('test');
});

it('has a type of bulk', function () {
    expect($this->action->getType())->toBe('bulk');
});

it('has an array form', function () {
    expect($this->action->toArray())->toEqual([
        'type' => 'bulk',
        'hasAction' => false,
        'name' => 'test',
        'label' => 'Test',
        'confirm' => null,
        'deselect' => false,
        'meta' => []
    ]);
});