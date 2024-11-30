<?php

use Honed\Table\Confirm\Confirm;

beforeEach(function () {
    $this->confirm = Confirm::make();
});

it('has an array form', function () {
    expect($this->confirm->toArray())->toEqual([
        'title' => null,
        'description' => null,
        'cancel' => null,
        'success' => null,
        'intent' => null
    ]);
});

it('can be made', function () {
    expect(Confirm::make('Are you sure?', 'This action cannot be undone.', 'Cancel', 'Submit', Confirm::Destructive))->toBeInstanceOf(Confirm::class)
        ->toArray()->toEqual([
            'title' => 'Are you sure?',
            'description' => 'This action cannot be undone.',
            'cancel' => 'Cancel',
            'success' => 'Submit',
            'intent' => Confirm::Destructive
        ]);
});

