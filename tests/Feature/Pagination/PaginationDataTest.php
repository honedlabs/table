<?php

declare(strict_types=1);

use Honed\Table\Pagination\PaginationData;

beforeEach(function () {
    $this->data = new PaginationData(false);
});

it('makes pagination data from collection', function () {
    expect(PaginationData::make(collect([])))
        ->toBeInstanceOf(PaginationData::class)
        ->isEmpty()->toBeTrue();
});

it('sets empty', function () {
    expect($this->data)
        ->isEmpty()->toBeFalse()
        ->empty()->toBe($this->data)
        ->isEmpty()->toBeTrue();
});

it('has array representation', function () {
    expect($this->data)
        ->toArray()->toBe([
            'empty' => false,
        ]);
});
