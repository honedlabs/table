<?php

use Honed\Core\Link\Proxies\HigherOrderLink;
use Honed\Table\Actions\InlineAction;
use Honed\Table\Confirm\Proxies\HigherOrderConfirm;

beforeEach(function () {
    $this->test = InlineAction::make('test');
});

it('is type inline', function () {
    expect($this->test->getType())->toBe('action:inline');
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
        ]);
});

it('has array representation with link', function () {
    expect($this->test->link('/products')->toArray())
        ->toBeArray()
        ->toHaveKeys([
            'name',
            'label',
            'type',
            'meta',
            'action',
            'confirm',
            'url',
            'method',
        ]);
});

it('forwards calls to link', function () {
    expect($this->test->link)->toBeInstanceOf(HigherOrderLink::class);
});

it('forwards calls to confirm', function () {
    expect($this->test->confirm)->toBeInstanceOf(HigherOrderConfirm::class);
});
