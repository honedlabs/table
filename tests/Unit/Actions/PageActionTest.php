<?php

use Honed\Table\Actions\PageAction;

beforeEach(function () {
    $this->action = PageAction::make('test');
});

it('has a type of page', function () {
    expect($this->action->getType())->toBe('page');
});

it('has an array form', function () {
    expect($this->action->toArray())->toEqual([
        'type' => 'page',
        'name' => 'test',
        'label' => 'Test',
        'meta' => [],
    ]);
});

it('forwards calls to url', function () {
    expect($this->action->url->url('/products'))->toBeInstanceOf(PageAction::class)
        ->toArray()->toEqual([
            'type' => 'page',
            'name' => 'test',
            'label' => 'Test',
            'meta' => [],
            'url' => '/products',
            'method' => 'get',
        ]);
});
