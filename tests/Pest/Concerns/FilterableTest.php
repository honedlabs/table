<?php

use Honed\Table\Concerns\Filterable;
use Honed\Table\Filters\Filter;
use Honed\Table\Tests\Stubs\Product;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class FilterableTest
{
    use Filterable;

    protected $filters;
}

class FilterableMethodTest extends FilterableTest
{
    public function filters(): array
    {
        return [
            Filter::make('test'),
        ];
    }
}

beforeEach(function () {
    $this->test = new FilterableTest;
    $this->method = new FilterableMethodTest;
});

it('is empty by default', function () {
    expect($this->test)
        ->hasFilters()->toBeFalse();

    expect($this->method)
        ->hasFilters()->toBeTrue()
        ->getFilters()->toHaveCount(1);
});

it('sets filters', function () {
    $this->test->setFilters([Filter::make('test')]);

    expect($this->test)
        ->hasFilters()->toBeTrue()
        ->getFilters()->scoped(fn ($filters) => $filters
            ->toBeCollection()
            ->toHaveCount(1)
            ->first()->scoped(fn ($filter) => $filter
                ->toBeInstanceOf(Filter::class)
                ->getAttribute()->toBe('test')
            )
        );
});

it('rejects null values', function () {
    $this->test->setFilters([Filter::make('test')]);
    $this->test->setFilters(null);

    expect($this->test)
        ->hasFilters()->toBeTrue()
        ->getFilters()->toHaveCount(1);
});

it('retrieves filters from method', function () {
    expect($this->method)
        ->hasFilters()->toBeTrue()
        ->getFilters()->scoped(fn ($filters) => $filters
            ->toBeCollection()
            ->toHaveCount(1)
            ->first()->scoped(fn ($filter) => $filter
                ->toBeInstanceOf(Filter::class)
                ->getAttribute()->toBe('test')
            )
        );
});

it('applies filters', function () {
    $request = Request::create('/', HttpFoundationRequest::METHOD_GET, ['test' => 10]);
    $builder = Product::query();

    $this->method->filterQuery($builder, $request);

    expect($builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->toEqual([
            [
                'type' => 'Basic',
                'column' => 'test',
                'operator' => '=',
                'value' => 10,
                'boolean' => 'and',
            ],
        ]);
});
