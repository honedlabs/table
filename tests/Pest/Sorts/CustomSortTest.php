<?php

use Honed\Table\Sorts\CustomSort;
use Honed\Table\Table;
use Honed\Table\Tests\Stubs\Product;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

beforeEach(function () {
    $this->name = 'created_at';
    $this->dir = CustomSort::Ascending;
    $this->sort = CustomSort::make($this->name);
    $this->builder = Product::query();
    Request::swap(Request::create('/', HttpFoundationRequest::METHOD_GET, [Table::SortKey => $this->name, Table::OrderKey => $this->dir]));
});

it('has a type', function () {
    expect(CustomSort::make('name'))->toBeInstanceOf(CustomSort::class)
        ->getType()->toBe('sort:custom');
});

it('checks if sorting based on name', function () {
    expect($this->sort->isSorting($this->name, $this->dir))
        ->toBeTrue();
    expect($this->sort->direction(CustomSort::Descending)->isSorting('updated_at', CustomSort::Descending))
        ->toBeFalse();
});

it('does not execute if the query is missing', function () {
    $this->sort->apply($this->builder, $this->name, $this->dir);
    expect($this->sort)
        ->isActive()->toBeTrue()
        ->hasQuery()->toBeFalse()
        ->getQuery()->toBeNull();

    expect($this->builder->getQuery()->orders)->toBeNull();
});

it('uses a custom query', function () {
    expect($this->sort->query(fn ($query, $direction) => $query->orderBy('custom', $direction)))
        ->toBeInstanceOf(CustomSort::class)
        ->hasQuery()->toBeTrue();

    $this->sort->apply($this->builder, $this->name, $this->dir);

    expect($this->builder->getQuery()->orders)
        ->toHaveCount(1)
        ->toEqual([
            [
                'column' => 'custom',
                'direction' => CustomSort::Ascending,
            ],
        ]);

    expect($this->sort)
        ->isActive()->toBeTrue()
        ->getActiveDirection()->toBe(CustomSort::Ascending);
});
