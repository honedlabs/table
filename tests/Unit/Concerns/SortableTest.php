<?php

use Honed\Table\Sorts\BaseSort;
use Honed\Table\Sorts\Sort;
use Honed\Table\Table;
use Honed\Table\Tests\Stubs\Product;
use Illuminate\Support\Facades\Request;

beforeEach(function () {
    $this->table = exampleTable();
    $this->blank = blankTable();
    Request::swap(Request::create('/', 'GET', [Table::DefaultSortKey => 'name', Table::DefaultOrderKey => 'asc']));
});

it('can determine if the table has no sorts', function () {
    expect($this->blank->missingSorts())->toBeTrue();
    expect($this->blank->hasSorts())->toBeFalse();

    expect($this->table->missingSorts())->toBeFalse();
    expect($this->table->hasSorts())->toBeTrue();
});

it('can set sorts', function () {
    $this->blank->setSorts([
        Sort::make('test'),
    ]);

    expect($this->blank->getSorts())
        ->toBeCollection()
        ->toHaveCount(1);
});

it('rejects null sorts', function () {
    $this->table->setSorts(null);

    expect($this->table->getSorts())->not->toBeEmpty();
});

it('can get sorts', function () {
    expect($this->table->getSorts())
        ->toBeCollection()
        ->not->toBeEmpty();

    expect($this->blank->getSorts())
        ->toBeCollection()
        ->toBeEmpty();
});

it('can apply sorts', function () {
    $query = Product::query();

    $this->table->sortQuery($query);

    expect($query->getQuery()->orders)
        ->toHaveCount(1)
        ->toEqual([
            [
                'column' => 'name',
                'direction' => 'asc',
            ],
        ]);

    expect($this->table->getSorts())
        ->first(fn (BaseSort $sort) => $sort->isActive())
        ->toBeInstanceOf(Sort::class)
        ->getName()->toBe('name')
        ->getDirection()->toBe('asc');
});

it('does not apply sort if the order is not active for a strict sort', function () {
    Request::swap(Request::create('/', 'GET', [Table::DefaultSortKey => 'name']));
    $query = Product::query();

    $this->table->sortQuery($query);

    expect($query->getQuery()->orders)
        ->toBeEmpty();

    expect($this->table->getSorts())
        ->first(fn (BaseSort $sort) => $sort->isActive())
        ->toBeNull();
})->skip();
