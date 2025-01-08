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

it('has a type', function () {
    expect(Sort::make('name'))->toBeInstanceOf(Sort::class)
        ->getType()->toBe('sort');
});

it('checks if sorting when agnostic', function () {
    expect($this->sort->isSorting($this->name, $this->dir))
        ->toBeTrue();
    expect($this->sort->isSorting('other', $this->dir))
        ->toBeFalse();
});

it('checks if sorting when not agnostic', function () {
    $this->sort->setDirection(Sort::Descending);
    expect($this->sort->isSorting($this->name, 'asc'))
        ->toBeFalse();
    expect($this->sort->isSorting($this->name, 'desc'))
        ->toBeTrue();
    expect($this->sort->isSorting('other', 'asc'))
        ->toBeFalse();
});
