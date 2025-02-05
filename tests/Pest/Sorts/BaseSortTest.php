<?php

use Honed\Table\Sorts\Sort;
use Honed\Table\Table;
use Honed\Table\Tests\Stubs\Product;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

beforeEach(function () {
    $this->name = 'created_at';
    $this->dir = Sort::Ascending;
    $this->sort = Sort::make($this->name);
    $this->builder = Product::query();
    Request::swap(Request::create('/', HttpFoundationRequest::METHOD_GET, [Table::SortKey => $this->name, Table::OrderKey => $this->dir]));
});

it('can be instantiated', function () {
    expect(new Sort('updated_at'))
        ->toBeInstanceOf(Sort::class)
        ->getAttribute()->toBe('updated_at')
        ->getLabel()->toBe('Updated at')
        ->getDirection()->toBeNull()
        ->getParameterName()->toBe('updated_at')
        ->isActive()->toBeFalse();
});

it('can be made', function () {
    expect(Sort::make('updated_at', 'Most recent'))
        ->toBeInstanceOf(Sort::class)
        ->getAttribute()->toBe('updated_at')
        ->getLabel()->toBe('Most recent')
        ->getParameterName()->toBe('updated_at')
        ->isActive()->toBeFalse();
});

it('checks if it is sorting', function () {
    expect($this->sort->isSorting($this->name, $this->dir))
        ->toBeTrue();
    expect($this->sort->isSorting('other', $this->dir))
        ->toBeFalse();
});

it('differentiates between dot notation attributes', function () {
    expect(Sort::make('users.created_at'))
        ->getAttribute()->toBe('users.created_at')
        ->getAlias()->toBeNull()
        ->getLabel()->toBe('Created at');
});

it('has array representation', function () {
    expect($this->sort->toArray())->toEqual([
        'name' => $this->name,
        'label' => 'Created at',
        'type' => 'sort',
        'active' => false,
        'meta' => [],
        'direction' => null,
    ]);
});

it('can be applied', function () {
    $this->sort->apply($this->builder, $this->name, $this->dir);
    expect($this->builder->getQuery()->orders)
        ->toHaveCount(1)
        ->toEqual([
            [
                'column' => $this->name,
                'direction' => $this->dir,
            ],
        ]);

    expect($this->sort)
        ->isActive()->toBeTrue()
        ->getActiveDirection()->toBe($this->dir);
});

it('is not applied if not matching', function () {
    $this->sort->apply($this->builder, 'other', $this->dir);
    expect($this->builder->getQuery()->orders)
        ->toBeNull();

    expect($this->sort)
        ->isActive()->toBeFalse()
        ->getActiveDirection()->toBe($this->dir);
});
