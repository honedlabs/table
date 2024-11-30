<?php

use Honed\Table\Actions\InlineAction;

beforeEach(function () {
    $this->action = InlineAction::make('test');
});

it('has a type of inline', function () {
    expect($this->action->getType())->toBe('inline');
});

it('has an array form', function () {
    expect($this->action->toArray())->toEqual([
        'type' => 'inline',
        'name' => 'test',
        'label' => 'Test',
        'confirm' => null,
        'hasAction' => false,
        'meta' => []
    ]);
});

it('forwards calls to url', function () {
    expect($this->action->url->url('/products'))->toBeInstanceOf(InlineAction::class)
        ->toArray()->toEqual([
            'type' => 'inline',
            'name' => 'test',
            'label' => 'Test',
            'confirm' => null,
            'hasAction' => false,
            'meta' => [],
            'url' => '/products',
            'method' => 'get'
        ]);
});

it('forwards calls to confirm', function () {
    expect($this->action->confirm->description('Are you sure?'))->toBeInstanceOf(InlineAction::class)
        ->toArray()->toEqual([
            'type' => 'inline',
            'name' => 'test',
            'label' => 'Test',
            'hasAction' => false,
            'meta' => [],
            'confirm' => [
                'title' => null,
                'description' => 'Are you sure?',
                'cancel' => null,
                'success' => null,
                'intent' => null
            ]
        ]);
});