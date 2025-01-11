<?php

declare(strict_types=1);

use Honed\Table\Actions\BaseAction;

class BaseActionTest extends BaseAction {}

beforeEach(function () {
    $this->test = new BaseActionTest('view');
});

it('can be made', function () {
    expect(BaseActionTest::make('view'))
        ->toBeInstanceOf(BaseActionTest::class)
        ->getName()->toBe('view')
        ->getLabel()->toBe('View');
});

it('has array representation', function () {
    expect($this->test->toArray())
        ->toBeArray()
        ->toHaveKeys(['name', 'label', 'type', 'meta']);
});
