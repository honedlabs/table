<?php

use Honed\Table\Filters\CustomFilter;
use Illuminate\Database\Eloquent\Builder;

beforeEach(function () {
    $this->filter = CustomFilter::make('name');
});

it('can set a query', function () {
    expect($this->filter->query(fn (Builder $builder) => $builder->where('name', 'test')))
        ->toBeInstanceOf(CustomFilter::class)
        ->getQuery()->toBeInstanceOf(Closure::class);
});

it('can be set using setter', function () {
    $this->filter->setQuery(fn (Builder $builder) => $builder->where('name', 'test'));
    expect($this->filter->getQuery())->toBeInstanceOf(Closure::class);
});

it('does not accept null values', function () {
    $this->filter->query(fn (Builder $builder) => $builder->where('name', 'test'));
    expect($this->filter->getQuery())->toBeInstanceOf(Closure::class);
    $this->filter->setQuery(null);
    expect($this->filter->getQuery())->toBeInstanceOf(Closure::class);
});

it('checks if it has a query', function () {
    expect($this->filter->hasQuery())->toBeFalse();
});

it('has alias for `using` as `query`', function () {
    expect($this->filter->using(fn (Builder $builder) => $builder->where('name', 'test')))
        ->toBeInstanceOf(CustomFilter::class)
        ->getQuery()->toBeInstanceOf(Closure::class);
});
