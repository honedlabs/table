<?php

use Honed\Table\Confirm\Confirm;

beforeEach(function () {
    $this->confirm = Confirm::make();
});

it('can be instantiated', function () {
    expect(new Confirm('Title'))
        ->toBeInstanceOf(Confirm::class)
        ->getTitle()->toBe('Title');
});

it('has array representation', function () {
    expect($this->confirm->toArray())
        ->toBeArray()
        ->toHaveKeys(['title', 'description', 'cancel', 'success', 'intent']);
});

it('can be made', function () {
    expect(Confirm::make('Title'))
        ->toBeInstanceOf(Confirm::class)
        ->getTitle()->toBe('Title');
});
