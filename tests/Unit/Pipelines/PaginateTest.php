<?php

declare(strict_types=1);

use Honed\Refine\Search;
use Honed\Table\Columns\Column;
use Honed\Table\Exceptions\InvalidPaginatorException;
use Honed\Table\Pipelines\Paginate;
use Honed\Table\Table;
use Honed\Table\Tests\Stubs\Product;
use Illuminate\Support\Facades\Request;

beforeEach(function () {
    $this->records = 100;

    foreach (range(1, $this->records) as $i) {
        product();
    }

    $this->pipe = new Paginate();
    $this->next = fn ($table) => $table;

    $this->table = Table::make()
        ->resource(Product::query());
});

it('paginates default', function () {
    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getRecords())
        ->toBeArray()
        ->toHaveCount(config('table.default_pagination'));

    expect($this->table->getPaginationData())
        ->toHaveKeys([
            'empty',
            'prevLink',
            'nextLink',
            'currentPage',
            'total',
            'from',
            'to',
            'firstLink',
            'lastLink',
            'links'
        ])->{'links'}->toHaveCount(config('table.window') * 2 + 1);
});

it('paginates simple', function () {
    $this->table->paginator('simple');

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getRecords())
        ->toBeArray()
        ->toHaveCount(config('table.default_pagination'));

    expect($this->table->getPaginationData())
        ->toHaveKeys([
            'empty',
            'prevLink',
            'nextLink',
            'perPage',
            'currentPage',
        ]);
});

it('paginates cursor', function () {
    $this->table->paginator('cursor');

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getRecords())
        ->toBeArray()
        ->toHaveCount(config('table.default_pagination'));

    expect($this->table->getPaginationData())
        ->toHaveKeys([
            'empty',
            'prevLink',
            'nextLink',
            'perPage',
        ]);
});

it('paginates collection', function () {
    $this->table->paginator('collection');

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getRecords())
        ->toBeArray()
        ->toHaveCount($this->records);

    expect($this->table->getPaginationData())
        ->toHaveKeys([
            'empty'
        ]);
});

it('paginate fails', function () {
    $this->table->paginator('invalid');

    $this->pipe->__invoke($this->table, $this->next);
    
})->throws(InvalidPaginatorException::class);

it('changes per page', function () {
    $count = 25;

    $request = Request::create('/', 'GET', [
        config('table.record_key') => $count
    ]);

    $this->table->pagination([10, 25, 50])->request($request);

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getRecords())
        ->toHaveCount($count);

    expect($this->table->getRecordsPerPage())
        ->toHaveCount(3);
});

it('changes per page with restrictions', function () {
    $count = 20;

    $request = Request::create('/', 'GET', [
        config('table.record_key') => $count
    ]);

    $this->table->pagination([10, 25, 50])->request($request);

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getRecords())
        ->toHaveCount(config('table.default_pagination'));

    expect($this->table->getRecordsPerPage())
        ->toHaveCount(3);
});

it('changes default per page', function () {
    $count = 25;

    $request = Request::create('/', 'GET', [
        config('table.record_key') => 20
    ]);

    $this->table->pagination([10, 25, 50])
        ->defaultPagination($count)
        ->request($request);

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getRecords())
        ->toHaveCount($count);

    expect($this->table->getRecordsPerPage())
        ->toHaveCount(3);
});

it('preserves query string', function () {
    $request = Request::create('/', 'GET', [
        'name' => 'test'
    ]);

    $this->table->request($request);

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getRecords())
        ->toBeArray()
        ->toHaveCount(config('table.default_pagination'));
});

it('fails to paginate scope', function () {
    $this->table->scope('scope')
        ->pagination([10, 25, 50]);

    $request = Request::create('/', 'GET', [
        config('table.record_key') => 25,
        config('table.page_key') => 2
    ]);

    $this->table->request($request);

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getRecords())
        ->toBeArray()
        ->toHaveCount(config('table.default_pagination'));

    expect($this->table->getPaginationData())
        ->{'currentPage'}->toBe(1);
    
    expect($this->table->getRecordsPerPage())
        ->toHaveCount(3);
});

it('paginates scope', function () {
    $this->table->scope('scope')
        ->pagination([10, 25, 50]);

    $request = Request::create('/', 'GET', [
        $this->table->formatScope(config('table.record_key')) => 25,
        $this->table->formatScope(config('table.page_key')) => 2
    ]);

    $this->table->request($request);

    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getRecords())
        ->toBeArray()
        ->toHaveCount(25);

    expect($this->table->getRecordsPerPage())
        ->toHaveCount(3);
});
