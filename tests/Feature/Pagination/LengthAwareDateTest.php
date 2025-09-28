<?php

declare(strict_types=1);

use Honed\Table\Pagination\LengthAwareData;
use Illuminate\Pagination\LengthAwarePaginator;

beforeEach(function () {
    $this->data = new LengthAwareData(false, 'prevLink', 'nextLink', 10, 1, 0, 0, 0, 'firstLink', 'lastLink', []);
});

it('makes pagination data from cursor paginator', function () {
    $paginator = new LengthAwarePaginator(collect([]), 0, 10);

    expect(LengthAwareData::make($paginator))
        ->toBeInstanceOf(LengthAwareData::class)
        ->isEmpty()->toBeTrue();
});

it('has array representation', function () {
    expect($this->data)
        ->toArray()->toBe([
            'empty' => false,
            'prevLink' => 'prevLink',
            'nextLink' => 'nextLink',
            'perPage' => 10,
            'currentPage' => 1,
            'total' => 0,
            'from' => 0,
            'to' => 0,
            'firstLink' => 'firstLink',
            'lastLink' => 'lastLink',
            'links' => [],
        ]);
});
