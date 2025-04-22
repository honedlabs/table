<?php

declare(strict_types=1);

use Honed\Table\Tests\Stubs\Product;
use Honed\Table\Pipelines\RefineSorts;
use Honed\Table\Table;
use Honed\Refine\Sort;
use Illuminate\Support\Facades\Request;

beforeEach(function () {
    $this->pipe = new RefineSorts();
    $this->closure = fn ($refine) => $refine;

    $sorts = [
        Sort::make('name')
            ->default(),
        
        Sort::make('price'),
    ];

    $this->table = Table::make()
        ->resource(Product::query())
        ->sorts($sorts);
});

it('does not refine', function () {
    $this->pipe->__invoke($this->table, $this->closure);

    expect($this->table->getResource()->getQuery()->wheres)
        ->toBeEmpty();
});

it('refines default', function () {
    $request = Request::create('/', 'GET', [
        'invalid' => 'test'
    ]);

    $this->table->request($request);

    $this->pipe->__invoke($this->table, $this->closure);

    $builder = $this->table->getResource();

    expect($builder->getQuery()->orders)
        ->toBeOnlyOrder($builder->qualifyColumn('name'), 'asc');
});

it('refines', function () {
    $request = Request::create('/', 'GET', [
        config('table.sort_key') => 'price'
    ]);

    $this->table->request($request);

    $this->pipe->__invoke($this->table, $this->closure);

    $builder = $this->table->getResource();

    expect($builder->getQuery()->orders)
        ->toBeOnlyOrder($builder->qualifyColumn('price'), 'asc');
});

it('refines directionally', function () {
    $request = Request::create('/', 'GET', [
        config('table.sort_key') => '-price'
    ]);

    $this->table->request($request);

    $this->pipe->__invoke($this->table, $this->closure);

    $builder = $this->table->getResource();

    expect($builder->getQuery()->orders)
        ->toBeOnlyOrder($builder->qualifyColumn('price'), 'desc');
});

it('disables', function () {
    $request = Request::create('/', 'GET', [
        config('table.sort_key') => 'price'
    ]);

    $this->table->request($request)->exceptSorts();

    $this->pipe->__invoke($this->table, $this->closure);

    $builder = $this->table->getResource();

    expect($builder->getQuery()->orders)
        ->toBeEmpty();
});

describe('scope', function () {
    beforeEach(function () {
        $this->table = $this->table->scope('scope');
    });

    it('refines default', function () {
        $request = Request::create('/', 'GET', [
            config('refine.sorts_key') => 'price'
        ]);

        $this->table->request($request);

        $this->pipe->__invoke($this->table, $this->closure);

        $builder = $this->table->getResource();

        expect($builder->getQuery()->orders)
            ->toBeOnlyOrder($builder->qualifyColumn('name'), 'asc');
    });

    it('refines', function () {
        $request = Request::create('/', 'GET', [
            $this->table->formatScope(config('table.sort_key')) => 'price'
        ]);

        $this->table->request($request);

        $this->pipe->__invoke($this->table, $this->closure);

        $builder = $this->table->getResource();

        expect($builder->getQuery()->orders)
            ->toBeOnlyOrder($builder->qualifyColumn('price'), 'asc');
    }); 
});