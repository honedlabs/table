<?php

declare(strict_types=1);

use Honed\Table\Table;

beforeEach(function () {
    $this->test = Table::make();
});

it('can be made', function () {
    expect(Table::make())->toBeInstanceOf(Table::class);
});

it('has array representation', function () {
    expect($this->test)
        ->toArray()
        ->toBeArray()
        ->toHaveKeys([
            'id',
            'key',
            'records',
            'columns',
            'actions',
            'filters',
            'sorts',
            'paginator',
            'toggleable',
            'sort',
            'order',
            'count',
            'search',
            'toggle',
            'endpoint',
        ]);
})->todo();

it('accepts a request to use', function () {
    $this->test->build(request());
})->todo();
