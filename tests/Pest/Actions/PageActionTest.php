<?php

use Honed\Core\Link\Proxies\HigherOrderLink;
use Honed\Table\Actions\PageAction;

beforeEach(function () {
    $this->test = PageAction::make('test');
});

it('is type page', function () {
    expect($this->test->getType())->toBe('action:page');
});

it('has array representation', function () {
    expect($this->test->toArray())
        ->toBeArray()
        ->toHaveKeys([
            'name',
            'label',
            'type',
            'meta',
        ]);
});

it('has array representation with link', function () {
    expect($this->test->link('/products')->toArray())
        ->toHaveKeys([
            'name',
            'label',
            'type',
            'meta',
            'url',
            'method',
        ]);
});

it('forwards calls to link', function () {
    expect($this->test->link)->toBeInstanceOf(HigherOrderLink::class);
});
