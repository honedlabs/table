<?php

declare(strict_types=1);

use Honed\Table\Pagination\SimpleData;
use Illuminate\Pagination\Paginator;

beforeEach(function () {
    $this->data = new SimpleData(false, 'prevLink', 'nextLink', 10, 1);
});

it('makes pagination data from Simple paginator', function () {
    $paginator = new Paginator(collect([]), 10);

    expect(SimpleData::make($paginator))
        ->toBeInstanceOf(SimpleData::class)
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
        ]);
});
