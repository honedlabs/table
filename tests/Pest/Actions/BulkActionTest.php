<?php

use Honed\Table\Actions\BulkAction;

beforeEach(function () {
    $this->test = BulkAction::make('test');
});

it('is type bulk', function () {
    expect($this->test->getType())->toBe('action:bulk');
});

it('has array representation', function () {
    expect($this->test->toArray())
        ->toBeArray()
        ->toHaveKeys([
            'name',
            'label',
            'type',
            'meta',
            'action',
            'confirm',
            'deselect',
        ]);
});
