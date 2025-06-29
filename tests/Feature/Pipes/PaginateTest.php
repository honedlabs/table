<?php

declare(strict_types=1);

use Honed\Table\Pipes\Paginate;
use Honed\Table\Table;
use Illuminate\Support\Facades\Request;
use Workbench\App\Models\Product;

beforeEach(function () {
    Product::factory(100)->create();

    $this->pipe = new Paginate();

    $this->table = Table::make()->for(Product::class);
});

it('paginates length aware', function () {
    $this->pipe->instance($this->table->lengthAwarePaginate());

    $this->pipe->run();

    expect($this->table->getRecords())
        ->toBeArray()
        ->toHaveCount($this->table->getDefaultPerPage());

    expect($this->table->getPagination())
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
            'links',
        ])
        ->{'links'}->toHaveCount($this->table->getWindow() * 2 + 1);
});

it('paginates simple', function () {
    $this->pipe->instance($this->table->simplePaginate());

    $this->pipe->run();

    expect($this->table->getRecords())
        ->toBeArray()
        ->toHaveCount($this->table->getDefaultPerPage());

    expect($this->table->getPagination())
        ->toHaveKeys([
            'empty',
            'prevLink',
            'nextLink',
            'perPage',
            'currentPage',
        ]);
});

it('paginates cursor', function () {
    $this->pipe->instance($this->table->cursorPaginate());

    $this->pipe->run();

    expect($this->table->getRecords())
        ->toBeArray()
        ->toHaveCount($this->table->getDefaultPerPage());

    expect($this->table->getPagination())
        ->toHaveKeys([
            'empty',
            'prevLink',
            'nextLink',
            'perPage',
        ]);
});

it('paginates collection', function () {
    $this->pipe->instance($this->table->paginate(false));

    $this->pipe->run();

    expect($this->table->getRecords())
        ->toBeArray()
        ->toHaveCount(100);

    expect($this->table->getPagination())
        ->toHaveKeys([
            'empty',
        ]);
});

it('errors if an invalid paginator is passed', function () {
    $this->pipe->instance($this->table->paginate('invalid'));

    $this->pipe->run();
})->throws(InvalidArgumentException::class);

it('changes per page', function () {
    $count = 25;

    $request = Request::create('/', 'GET', [
        $this->table->getRecordKey() => $count,
    ]);

    $this->table->perPage([10, 25, 50])->request($request);

    $this->pipe->instance($this->table->paginate());

    $this->pipe->run();

    expect($this->table->getRecords())
        ->toHaveCount($count);

    expect($this->table->getPageOptions())
        ->toHaveCount(3);
});

it('changes per page with restrictions', function () {
    $count = 20;

    $request = Request::create('/', 'GET', [
        $this->table->getRecordKey() => $count,
    ]);

    $this->table->perPage([10, 25, 50])->request($request);

    $this->pipe->instance($this->table->paginate());

    $this->pipe->run();

    expect($this->table->getRecords())
        ->toHaveCount($this->table->getDefaultPerPage());

    expect($this->table->getPageOptions())
        ->toHaveCount(3);
});

it('changes default per page', function () {
    $count = 25;

    $request = Request::create('/', 'GET', [
        $this->table->getRecordKey() => 20,
    ]);

    $this->table
        ->perPage([10, 25, 50])
        ->defaultPerPage($count)
        ->request($request);

    $this->pipe->instance($this->table->paginate());

    $this->pipe->run();

    expect($this->table->getRecords())
        ->toHaveCount($count);

    expect($this->table->getPageOptions())
        ->toHaveCount(3);
});
