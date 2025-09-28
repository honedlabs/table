<?php

declare(strict_types=1);

use Honed\Table\Pagination\CursorData;
use Illuminate\Pagination\CursorPaginator;

beforeEach(function () {
    $this->data = new CursorData(false, 'prevLink', 'nextLink', 10);
});

it('makes pagination data from cursor paginator', function () {
    $paginator = new CursorPaginator(collect([]), 10);

    expect(CursorData::make($paginator))
        ->toBeInstanceOf(CursorData::class)
        ->isEmpty()->toBeTrue();
});

it('has array representation', function () {
    expect($this->data)
        ->toArray()->toBe([
            'empty' => false,
            'prevLink' => 'prevLink',
            'nextLink' => 'nextLink',
            'perPage' => 10,
        ]);
});
