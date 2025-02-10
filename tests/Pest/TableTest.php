<?php

declare(strict_types=1);

use Honed\Table\Table as HonedTable;
use Honed\Table\Tests\Fixtures\Table;
use Illuminate\Support\Arr;

beforeEach(function () {
    $this->test = Table::make();

    foreach (\range(1, 100) as $i) {
        product();
    }
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
})->skip();

it('accepts a request to use', function () {
    // $this->test->build(request());
    dd($this->test->toArray());
});
